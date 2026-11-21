<?php
namespace App\Services;

use Carbon\Carbon;

/**
 * Validates and normalises raw source rows. Never silently fixes values:
 * every problem becomes an error entry (code, severity, field, message).
 */
class DataValidationService
{
    public const KIND_MASTER = 'PROJECT_MASTER';
    public const KIND_UPDATES = 'PROJECT_UPDATES';
    public const KIND_PRODUCTION = 'PRODUCTION_DATA';

    /** Detect source kind from structure, not file name (FR-011). */
    public function detectKind(array $headersOrKeys, bool $isJson): ?string
    {
        $k = array_map('strtolower', $headersOrKeys);
        if ($isJson) return in_array('records', $k) ? self::KIND_PRODUCTION : null;
        if (in_array('update_id', $k) && in_array('message', $k)) return self::KIND_UPDATES;
        if (in_array('project_id', $k) && in_array('deadline', $k) && in_array('target', $k)) return self::KIND_MASTER;
        return null;
    }

    public function parseDate(?string $v, bool $withTime = false): ?Carbon
    {
        $v = trim((string) $v);
        if ($v === '') return null;
        $fmt = $withTime ? ['Y-m-d\TH:i:s', 'Y-m-d H:i:s', 'Y-m-d\TH:i'] : ['Y-m-d'];
        foreach ($fmt as $f) {
            try { $d = Carbon::createFromFormat($f, $v); } catch (\Throwable) { continue; }
            if ($d && $d->format($f) === $v) return $withTime ? $d : $d->startOfDay();
        }
        return null;
    }

    /** @return array{0:?array,1:array} [normalised|null, errors] */
    public function master(array $r, string $ref, callable $known = null): array
    {
        $e = [];
        $r = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $r)
            + array_fill_keys(['project_id', 'customer', 'project_name', 'project_type', 'pic', 'start_date', 'deadline', 'target', 'target_unit', 'reported_actual', 'reported_progress_pct', 'reported_status', 'last_reported_update_at'], '');
        foreach (['project_id', 'customer', 'project_name', 'start_date', 'deadline', 'target'] as $f) {
            if (($r[$f] ?? '') === '') $e[] = $this->err('MISSING_FIELD', 'ERROR', $f, "Field wajib '$f' kosong.", $ref);
        }
        foreach (['pic', 'project_type', 'target_unit', 'reported_status', 'reported_actual', 'reported_progress_pct', 'last_reported_update_at'] as $f) {
            if (($r[$f] ?? '') === '') $e[] = $this->err('MISSING_OPTIONAL', 'WARNING', $f, "Field '$f' kosong; nilai tidak diasumsikan.", $ref);
        }
        $start = $this->parseDate($r['start_date'] ?? null);
        $dead = $this->parseDate($r['deadline'] ?? null);
        if (($r['start_date'] ?? '') !== '' && ! $start) $e[] = $this->err('INVALID_DATE', 'ERROR', 'start_date', 'Format start_date harus YYYY-MM-DD.', $ref);
        if (($r['deadline'] ?? '') !== '' && ! $dead) $e[] = $this->err('INVALID_DATE', 'ERROR', 'deadline', 'Format deadline harus YYYY-MM-DD.', $ref);
        if ($start && $dead && $dead->lt($start)) $e[] = $this->err('RANGE', 'ERROR', 'deadline', 'deadline lebih awal dari start_date.', $ref);
        foreach (['target', 'reported_actual', 'reported_progress_pct'] as $f) {
            if (($r[$f] ?? '') !== '' && ! is_numeric($r[$f])) $e[] = $this->err('INVALID_NUMBER', 'ERROR', $f, "Nilai '$f' bukan angka: {$r[$f]}", $ref);
        }
        if (is_numeric($r['target'] ?? null) && (float) $r['target'] <= 0) $e[] = $this->err('RANGE', 'ERROR', 'target', 'target harus > 0.', $ref);
        if (is_numeric($r['reported_progress_pct'] ?? null) && ((float) $r['reported_progress_pct'] < 0 || (float) $r['reported_progress_pct'] > 100))
            $e[] = $this->err('RANGE', 'ERROR', 'reported_progress_pct', 'progress di luar rentang 0-100.', $ref);
        if (is_numeric($r['reported_actual'] ?? null) && (float) $r['reported_actual'] < 0) $e[] = $this->err('RANGE', 'ERROR', 'reported_actual', 'reported_actual negatif.', $ref);
        $lr = null;
        if (($r['last_reported_update_at'] ?? '') !== '') {
            $lr = $this->parseDate($r['last_reported_update_at'], true);
            if (! $lr) $e[] = $this->err('INVALID_DATE', 'ERROR', 'last_reported_update_at', 'Format datetime harus YYYY-MM-DDTHH:MM:SS.', $ref);
        }
        if ($this->hasError($e)) return [null, $e];
        return [[
            'project_code' => strtoupper($r['project_id']), 'customer' => $r['customer'], 'name' => $r['project_name'],
            'project_type' => $r['project_type'] ?: null, 'pic' => $r['pic'] ?: null, 'start_date' => $start->toDateString(),
            'deadline' => $dead->toDateString(), 'target' => (float) $r['target'], 'target_unit' => $r['target_unit'] ?: null,
            'reported_actual' => ($r['reported_actual'] ?? '') === '' ? null : (float) $r['reported_actual'],
            'reported_progress_pct' => ($r['reported_progress_pct'] ?? '') === '' ? null : (float) $r['reported_progress_pct'],
            'reported_status' => ($r['reported_status'] ?? '') === '' ? null : strtoupper($r['reported_status']),
            'last_reported_update_at' => $lr?->toDateTimeString(),
        ], $e];
    }

    public function update(array $r, string $ref, callable $projectExists): array
    {
        $e = [];
        $r = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $r);
        foreach (['update_id', 'project_id', 'timestamp', 'source_type', 'message'] as $f) {
            if (($r[$f] ?? '') === '') $e[] = $this->err('MISSING_FIELD', 'ERROR', $f, "Field wajib '$f' kosong.", $ref);
        }
        if (($r['source_name'] ?? '') === '') $e[] = $this->err('MISSING_OPTIONAL', 'WARNING', 'source_name', 'source_name kosong.', $ref);
        $ts = $this->parseDate($r['timestamp'] ?? null, true);
        if (($r['timestamp'] ?? '') !== '' && ! $ts) $e[] = $this->err('INVALID_DATE', 'ERROR', 'timestamp', 'Format datetime harus YYYY-MM-DDTHH:MM:SS.', $ref);
        if (($r['project_id'] ?? '') !== '' && ! $projectExists(strtoupper($r['project_id'])))
            $e[] = $this->err('UNKNOWN_PROJECT', 'ERROR', 'project_id', "project_id {$r['project_id']} tidak ada pada project master.", $ref);
        if ($this->hasError($e)) return [null, $e];
        return [['update_code' => $r['update_id'], 'project_code' => strtoupper($r['project_id']), 'occurred_at' => $ts->toDateTimeString(),
            'source_type' => strtoupper($r['source_type']), 'source_name' => $r['source_name'] ?: null, 'message' => $r['message']], $e];
    }

    public function production(array $r, string $ref, callable $projectExists): array
    {
        $e = [];
        foreach (['source_record_id', 'project_id', 'source_system', 'record_type', 'timestamp'] as $f) {
            if (! isset($r[$f]) || trim((string) $r[$f]) === '') $e[] = $this->err('MISSING_FIELD', 'ERROR', $f, "Field wajib '$f' kosong.", $ref);
        }
        $ts = $this->parseDate($r['timestamp'] ?? null, true);
        if (isset($r['timestamp']) && $r['timestamp'] !== '' && ! $ts) $e[] = $this->err('INVALID_DATE', 'ERROR', 'timestamp', 'Format datetime harus YYYY-MM-DDTHH:MM:SS.', $ref);
        if (! empty($r['project_id']) && ! $projectExists(strtoupper($r['project_id'])))
            $e[] = $this->err('UNKNOWN_PROJECT', 'ERROR', 'project_id', "project_id {$r['project_id']} tidak ada pada project master.", $ref);
        $catalog = config('ontrack.metrics');
        $meta = array_flip(config('ontrack.meta_fields'));
        foreach ($r as $k => $v) {
            if (isset($meta[$k]) || is_array($v) || $v === null) continue;
            $isNum = isset($catalog[$k]) && in_array($catalog[$k]['kind'], ['quantity', 'pending', 'quality', 'stage', 'target', 'usage']);
            if ($isNum && ! is_numeric($v)) $e[] = $this->err('INVALID_NUMBER', 'ERROR', $k, "Nilai '$k' bukan angka: $v", $ref);
            elseif ($isNum && (float) $v < 0) $e[] = $this->err('RANGE', 'ERROR', $k, "Nilai '$k' negatif.", $ref);
        }
        if (isset($r['scanners'])) {
            if (! is_array($r['scanners'])) $e[] = $this->err('INVALID_STRUCTURE', 'ERROR', 'scanners', 'scanners harus berupa array.', $ref);
            else foreach ($r['scanners'] as $i => $s) {
                if (empty($s['scanner_id']) || empty($s['status'])) $e[] = $this->err('INVALID_STRUCTURE', 'ERROR', "scanners[$i]", 'scanner_id/status wajib.', $ref);
            }
        }
        if ($this->hasError($e)) return [null, $e];
        $payload = $r;
        foreach (['source_record_id', 'project_id', 'source_system', 'record_type', 'timestamp'] as $f) unset($payload[$f]);
        return [['source_record_id' => trim($r['source_record_id']), 'project_code' => strtoupper(trim($r['project_id'])),
            'source_system' => $r['source_system'], 'record_type' => $r['record_type'], 'observed_at' => $ts->toDateTimeString(), 'payload' => $payload], $e];
    }

    private function err(string $code, string $sev, ?string $field, string $msg, string $ref): array
    {
        return ['code' => $code, 'severity' => $sev, 'field' => $field, 'message' => $msg, 'record_ref' => $ref];
    }

    private function hasError(array $e): bool { return (bool) array_filter($e, fn ($x) => $x['severity'] === 'ERROR'); }
}
