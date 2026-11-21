<?php
namespace App\Services;

use App\Models\{DataImportBatch, DataValidationError, EvidenceRecord, Finding, ProductionRecord, Project, ProjectUpdate, Setting};
use App\Support\CurrentUser;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Upload -> validate -> normalise -> persist (transaction) -> context -> evidence -> interpret
 * -> attention -> re-verify. Repeatable and safe (FR-011..014, BR-009/012).
 */
class DataImportService
{
    private const ORDER = [DataValidationService::KIND_MASTER => 1, DataValidationService::KIND_UPDATES => 2, DataValidationService::KIND_PRODUCTION => 3];

    public function __construct(private DataValidationService $validator, private EvidenceService $evidence, private ProjectContextService $contexts,
        private InterpretationService $interpretation, private AttentionService $attention, private IssueVerificationService $verification) {}

    /** @param array<int,array{path:string,name:string}> $files */
    public function importFiles(array $files, ?string $asOf = null): array
    {
        if ($asOf) Setting::write('as_of', CarbonImmutable::parse($asOf)->toDateTimeString());
        $parsed = [];
        foreach ($files as $f) $parsed[] = $this->parse($f['path'], $f['name']);
        usort($parsed, fn ($a, $b) => (self::ORDER[$a['kind']] ?? 9) <=> (self::ORDER[$b['kind']] ?? 9));
        return array_map(fn ($p) => $this->process($p), $parsed);
    }

    public function importFile(string $path, string $name, ?string $asOf = null): DataImportBatch
    {
        return $this->importFiles([['path' => $path, 'name' => $name]], $asOf)[0];
    }

    /**
     * Manual entry (forms) goes through exactly the same validation/persistence/interpretation
     * pipeline as file uploads. $rows use the same column names as the CSV/JSON sources.
     */
    public function importRows(string $kind, array $rows, string $label): DataImportBatch
    {
        return $this->process(['path' => null, 'name' => $label, 'kind' => $kind, 'rows' => $rows, 'reject' => null, 'hash' => null, 'origin' => 'MANUAL']);
    }

    private function parse(string $path, string $name): array
    {
        $out = ['path' => $path, 'name' => $name, 'kind' => null, 'rows' => [], 'reject' => null, 'hash' => is_file($path) ? sha1_file($path) : null];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (! is_file($path)) return $out + ['reject' => 'File tidak ditemukan.'];
        if (! in_array($ext, config('ontrack.upload.extensions'))) return array_merge($out, ['reject' => "Tipe file .$ext tidak didukung (hanya CSV/JSON)."]);
        if (filesize($path) > config('ontrack.upload.max_kb') * 1024) return array_merge($out, ['reject' => 'Ukuran file melebihi batas ' . config('ontrack.upload.max_kb') . ' KB.']);
        $raw = file_get_contents($path);
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        $trim = ltrim($raw);
        if ($trim === '') return array_merge($out, ['reject' => 'File kosong.']);
        if ($trim[0] === '{' || $trim[0] === '[') {
            $j = json_decode($raw, true);
            if (! is_array($j)) return array_merge($out, ['reject' => 'JSON tidak valid: ' . json_last_error_msg()]);
            $kind = $this->validator->detectKind(array_keys($j), true);
            if (! $kind || ! is_array($j['records'] ?? null)) return array_merge($out, ['reject' => 'Struktur JSON tidak dikenali (diharapkan objek dengan "records").']);
            $out['kind'] = $kind; $out['rows'] = $j['records']; $out['generated_at'] = $j['generated_at'] ?? null;
            return $out;
        }
        $lines = preg_split('/\r\n|\n|\r/', $raw);
        $h = str_getcsv(array_shift($lines)); $h = array_map(fn ($x) => strtolower(trim($x)), $h);
        $kind = $this->validator->detectKind($h, false);
        if (! $kind) return array_merge($out, ['reject' => 'Struktur CSV tidak dikenali (header tidak cocok dengan Project Master atau Project Updates).']);
        $rows = []; $csv = fopen('php://memory', 'r+'); fwrite($csv, implode("\n", $lines)); rewind($csv); $n = 1;
        while (($r = fgetcsv($csv)) !== false) {
            $n++;
            if ($r === [null] || (count($r) === 1 && trim((string) $r[0]) === '')) continue;
            $row = []; foreach ($h as $i => $col) $row[$col] = $r[$i] ?? '';
            if (count($r) !== count($h)) $row['__column_mismatch'] = count($r);
            $row['__line'] = $n; $rows[] = $row;
        }
        fclose($csv);
        return array_merge($out, ['kind' => $kind, 'rows' => $rows]);
    }

    private function process(array $p): DataImportBatch
    {
        $batch = DataImportBatch::create(['file_name' => $p['name'], 'origin' => $p['origin'] ?? 'FILE', 'source_kind' => $p['kind'], 'file_hash' => $p['hash'], 'as_of' => Setting::asOf(),
            'status' => 'PROCESSING', 'created_by' => CurrentUser::id(), 'started_at' => now()]);
        if ($p['reject']) {
            $batch->update(['status' => 'REJECTED', 'message' => $p['reject'], 'finished_at' => now()]);
            DataValidationError::create(['batch_id' => $batch->id, 'source_kind' => $p['kind'], 'code' => 'FILE_REJECTED', 'severity' => 'ERROR', 'message' => $p['reject']]);
            ActivityLogService::log('DATA_IMPORT', "Import {$p['name']} ditolak: {$p['reject']}", null, null, $batch->id);
            return $batch;
        }
        // 1) validate + normalise (no DB writes yet apart from the error report)
        $valid = []; $errors = []; $invalid = 0;
        $known = Project::pluck('id', 'project_code')->all();
        if ($p['kind'] === DataValidationService::KIND_MASTER) $pending = [];
        foreach ($p['rows'] as $i => $row) {
            $ref = isset($row['__line']) ? 'baris ' . $row['__line'] : 'record #' . ($i + 1);
            if (isset($row['__column_mismatch'])) { $errors[] = ['code' => 'COLUMN_MISMATCH', 'severity' => 'ERROR', 'field' => null, 'record_ref' => $ref, 'message' => 'Jumlah kolom tidak sama dengan header.']; $invalid++; continue; }
            $exists = fn ($code) => isset($known[$code]);
            [$norm, $errs] = match ($p['kind']) {
                DataValidationService::KIND_MASTER => $this->validator->master($row, $ref),
                DataValidationService::KIND_UPDATES => $this->validator->update($row, $ref, $exists),
                default => $this->validator->production($row, $ref, $exists),
            };
            array_push($errors, ...$errs);
            if ($norm === null) { $invalid++; continue; }
            $norm['_ref'] = $ref;
            if ($p['kind'] === DataValidationService::KIND_MASTER) $known[$norm['project_code']] = true; // masters later in the same file
            $valid[] = $norm;
        }
        // 2) persist everything atomically
        $stats = ['new' => 0, 'dup' => 0, 'conflict' => 0];
        try {
            DB::transaction(function () use ($p, $valid, $batch, &$stats, &$errors) {
                $affected = [];
                $seen = [];
                foreach ($valid as $n) {
                    $res = match ($p['kind']) {
                        DataValidationService::KIND_MASTER => $this->saveMaster($n, $batch, $seen),
                        DataValidationService::KIND_UPDATES => $this->saveUpdate($n, $batch, $seen),
                        default => $this->saveProduction($n, $batch, $seen),
                    };
                    $stats[$res['stat']]++;
                    if ($res['event']) $errors[] = $res['event'] + ['record_ref' => $n['_ref']];
                    if ($res['project_id']) $affected[$res['project_id']] = true;
                }
                $this->postProcess(array_keys($affected), $batch);
            });
        } catch (\Throwable $e) {
            $this->storeErrors($batch, $p['kind'], $errors);
            $batch->update(['status' => 'FAILED', 'finished_at' => now(), 'total_records' => count($p['rows']), 'invalid_records' => $invalid,
                'message' => 'Import dibatalkan dan seluruh perubahan batch di-rollback: ' . $e->getMessage()]);
            ActivityLogService::log('DATA_IMPORT', "Import {$p['name']} GAGAL (rollback): {$e->getMessage()}", null, null, $batch->id);
            return $batch;
        }
        $this->storeErrors($batch, $p['kind'], $errors);
        $missing = collect($errors)->whereIn('code', ['MISSING_FIELD', 'MISSING_OPTIONAL'])->count();
        $warn = $invalid > 0 || $stats['conflict'] > 0 || collect($errors)->where('severity', 'WARNING')->isNotEmpty();
        $batch->update(['status' => $warn ? 'COMPLETED_WITH_WARNINGS' : 'COMPLETED', 'total_records' => count($p['rows']), 'valid_records' => count($valid), 'invalid_records' => $invalid,
            'new_records' => $stats['new'], 'duplicate_records' => $stats['dup'], 'conflicting_records' => $stats['conflict'], 'missing_field_count' => $missing, 'finished_at' => now(),
            'message' => "Diproses: {$stats['new']} baru, {$stats['dup']} duplikat dilewati, {$stats['conflict']} revisi/konflik, $invalid ditolak."]);
        ActivityLogService::log('DATA_IMPORT', "Import {$p['name']} ({$p['kind']}): " . $batch->message, null, null, $batch->id);
        return $batch->fresh();
    }

    private function storeErrors(DataImportBatch $b, ?string $kind, array $errors): void
    {
        foreach (array_chunk($errors, 200) as $chunk) {
            DataValidationError::insert(array_map(fn ($e) => ['batch_id' => $b->id, 'source_kind' => $kind, 'record_ref' => $e['record_ref'] ?? null, 'field' => $e['field'] ?? null,
                'code' => $e['code'], 'severity' => $e['severity'], 'message' => $e['message'], 'created_at' => now(), 'updated_at' => now()], $chunk));
        }
    }

    private function fp(array $data): string
    {
        unset($data['_ref']);
        $sort = function (&$a) use (&$sort) { if (is_array($a)) { ksort($a); foreach ($a as &$v) $sort($v); } };
        $sort($data);
        return sha1(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function diff(array $old, array $new): string
    {
        $d = [];
        foreach ($new as $k => $v) if ($k !== '_ref' && ($old[$k] ?? null) != $v) $d[] = "$k: " . json_encode($old[$k] ?? null) . ' → ' . json_encode($v);
        return implode('; ', array_slice($d, 0, 6));
    }

    private function saveMaster(array $n, DataImportBatch $b, array &$seen): array
    {
        $fp = $this->fp($n); $code = $n['project_code']; $attrs = $n; unset($attrs['_ref'], $attrs['project_code']);
        if (isset($seen[$code]) && $seen[$code] === $fp) return ['stat' => 'dup', 'project_id' => null, 'event' => ['code' => 'DUPLICATE_RECORD', 'severity' => 'INFO', 'field' => 'project_id', 'message' => "Duplikat identik $code dalam file dilewati."]];
        $seen[$code] = $fp;
        $p = Project::where('project_code', $code)->first();
        if (! $p) {
            $p = Project::create($attrs + ['project_code' => $code, 'fingerprint' => $fp, 'source_batch_id' => $b->id]);
            $this->evidence->ingestMaster($p, 1, $b->as_of->toDateTimeString());
            return ['stat' => 'new', 'project_id' => $p->id, 'event' => null];
        }
        if ($p->fingerprint === $fp) return ['stat' => 'dup', 'project_id' => null, 'event' => ['code' => 'DUPLICATE_RECORD', 'severity' => 'INFO', 'field' => 'project_id', 'message' => "Master $code identik dengan data tersimpan; dilewati."]];
        $old = $p->only(array_keys($attrs));
        $rev = (int) EvidenceRecord::where('origin_table', 'projects')->where('origin_id', $p->id)->max('revision') + 1;
        $older = $p->last_reported_update_at && ! empty($attrs['last_reported_update_at']) && CarbonImmutable::parse($attrs['last_reported_update_at'])->lt($p->last_reported_update_at);
        if ($older) {
            $tmp = (clone $p)->forceFill($attrs); $this->evidence->ingestMaster($tmp, $rev, $b->as_of->toDateTimeString());
            return ['stat' => 'conflict', 'project_id' => $p->id, 'event' => ['code' => 'OLDER_RECORD', 'severity' => 'WARNING', 'field' => 'last_reported_update_at', 'message' => "Master $code lebih lama dari data tersimpan; disimpan sebagai riwayat, tidak menjadi data terbaru."]];
        }
        $p->update($attrs + ['fingerprint' => $fp, 'revision' => $rev, 'source_batch_id' => $b->id]);
        $this->evidence->ingestMaster($p->fresh(), $rev, $b->as_of->toDateTimeString());
        return ['stat' => 'conflict', 'project_id' => $p->id, 'event' => ['code' => 'REVISED_RECORD', 'severity' => 'WARNING', 'field' => 'project_id', 'message' => "Master $code berubah (revisi $rev); nilai lama tetap tersimpan sebagai evidence historis. " . $this->diff($old, $attrs)]];
    }

    private function saveUpdate(array $n, DataImportBatch $b, array &$seen): array
    {
        $fp = $this->fp($n); $code = $n['update_code'];
        if (isset($seen["u$code"]) && $seen["u$code"] === $fp) return ['stat' => 'dup', 'project_id' => null, 'event' => ['code' => 'DUPLICATE_RECORD', 'severity' => 'INFO', 'field' => 'update_id', 'message' => "Duplikat identik $code dalam file dilewati."]];
        $seen["u$code"] = $fp;
        $pid = Project::where('project_code', $n['project_code'])->value('id');
        $cur = ProjectUpdate::where('update_code', $code)->orderByDesc('revision')->first();
        $row = ['update_code' => $code, 'project_id' => $pid, 'occurred_at' => $n['occurred_at'], 'source_type' => $n['source_type'], 'source_name' => $n['source_name'],
            'message' => $n['message'], 'fingerprint' => $fp, 'source_batch_id' => $b->id];
        if (! $cur) { $u = ProjectUpdate::create($row + ['revision' => 1]); $this->evidence->ingestUpdate($u); return ['stat' => 'new', 'project_id' => $pid, 'event' => null]; }
        if ($cur->fingerprint === $fp) return ['stat' => 'dup', 'project_id' => null, 'event' => ['code' => 'DUPLICATE_RECORD', 'severity' => 'INFO', 'field' => 'update_id', 'message' => "Update $code identik dengan data tersimpan; dilewati."]];
        $older = CarbonImmutable::parse($n['occurred_at'])->lt($cur->occurred_at);
        if (! $older) ProjectUpdate::where('update_code', $code)->update(['is_current' => false]);
        $u = ProjectUpdate::create($row + ['revision' => $cur->revision + 1, 'is_current' => ! $older]);
        $this->evidence->ingestUpdate($u);
        return ['stat' => 'conflict', 'project_id' => $pid, 'event' => ['code' => $older ? 'OLDER_RECORD' : 'REVISED_RECORD', 'severity' => 'WARNING', 'field' => 'update_id',
            'message' => "Update $code berubah isinya (revisi {$u->revision}); versi sebelumnya dipertahankan." . ($older ? ' Timestamp lebih lama sehingga tidak menjadi versi terbaru.' : '')]];
    }

    private function saveProduction(array $n, DataImportBatch $b, array &$seen): array
    {
        $fp = $this->fp($n); $code = $n['source_record_id'];
        if (isset($seen["p$code"]) && $seen["p$code"] === $fp) return ['stat' => 'dup', 'project_id' => null, 'event' => ['code' => 'DUPLICATE_RECORD', 'severity' => 'INFO', 'field' => 'source_record_id', 'message' => "Duplikat identik $code dalam file dilewati."]];
        $seen["p$code"] = $fp;
        $pid = Project::where('project_code', $n['project_code'])->value('id');
        $cur = ProductionRecord::where('source_record_id', $code)->orderByDesc('revision')->first();
        $row = ['source_record_id' => $code, 'project_id' => $pid, 'source_system' => $n['source_system'], 'record_type' => $n['record_type'], 'observed_at' => $n['observed_at'],
            'payload' => $n['payload'], 'fingerprint' => $fp, 'source_batch_id' => $b->id];
        if (! $cur) { $r = ProductionRecord::create($row + ['revision' => 1]); $this->evidence->ingestProduction($r); return ['stat' => 'new', 'project_id' => $pid, 'event' => null]; }
        if ($cur->fingerprint === $fp) return ['stat' => 'dup', 'project_id' => null, 'event' => ['code' => 'DUPLICATE_RECORD', 'severity' => 'INFO', 'field' => 'source_record_id', 'message' => "Record $code identik dengan data tersimpan; dilewati."]];
        $older = CarbonImmutable::parse($n['observed_at'])->lt($cur->observed_at);
        if (! $older) ProductionRecord::where('source_record_id', $code)->update(['is_current' => false]);
        $r = ProductionRecord::create($row + ['revision' => $cur->revision + 1, 'is_current' => ! $older]);
        $this->evidence->ingestProduction($r);
        return ['stat' => 'conflict', 'project_id' => $pid, 'event' => ['code' => $older ? 'OLDER_RECORD' : 'REVISED_RECORD', 'severity' => 'WARNING', 'field' => 'source_record_id',
            'message' => "Record $code berubah isinya (revisi {$r->revision}); versi lama dipertahankan sebagai riwayat." . ($older ? ' Timestamp lebih lama, tidak menjadi data terbaru.' : '')]];
    }

    /** Context -> evidence flags -> interpretation -> attention -> re-verification. */
    public function postProcess(array $projectIds, ?DataImportBatch $batch = null): void
    {
        if (! $projectIds) return;
        $before = $this->snapshot($projectIds);
        foreach (Project::whereIn('id', $projectIds)->get() as $p) $this->evidence->refreshProject($p, Setting::asOf());
        foreach ($this->contexts->forProjects($projectIds) as $ctx) {
            $findings = $this->interpretation->interpret($ctx, $batch?->id);
            $this->attention->assess($ctx, $findings, $batch?->id);
        }
        $toVerify = Finding::whereIn('project_id', $projectIds)->whereHas('followUps', fn ($q) => $q->where('status', 'RESOLVED'))
            ->where('verification_status', '!=', 'VERIFIED')->get();
        foreach ($toVerify as $f) $this->verification->verify($f, $f->followUps()->where('status', 'RESOLVED')->latest('id')->first(), 'DATA_REFRESH');
        $impact = $this->diffImpact($before, $this->snapshot($projectIds));
        if ($batch) $batch->update(['impact' => $impact]);
    }

    /** Level/score and active findings per project, used to explain the effect of a change. */
    private function snapshot(array $ids): array
    {
        $out = [];
        foreach (Project::whereIn('id', $ids)->get() as $p) {
            $a = \App\Models\AttentionAssessment::where('project_id', $p->id)->where('is_current', true)->latest('id')->first();
            $out[$p->id] = ['code' => $p->project_code, 'name' => $p->name, 'level' => $a?->level, 'score' => $a?->score,
                'findings' => Finding::where('project_id', $p->id)->where('is_active', true)->get()->mapWithKeys(fn ($f) => [$f->fingerprint => "[{$f->severity}] {$f->title}"])->all()];
        }
        return $out;
    }

    private function diffImpact(array $before, array $after): array
    {
        $rows = [];
        foreach ($after as $id => $a) {
            $b = $before[$id] ?? ['level' => null, 'score' => null, 'findings' => []];
            $opened = array_values(array_diff_key($a['findings'], $b['findings']));
            $closed = array_values(array_diff_key($b['findings'], $a['findings']));
            $changed = array_values(array_filter(array_intersect_key($a['findings'], $b['findings']), fn ($t, $k) => $t !== $b['findings'][$k], ARRAY_FILTER_USE_BOTH));
            $levelChanged = $b['level'] !== $a['level'];
            $scoreChanged = $b['score'] === null || $a['score'] === null || abs($b['score'] - $a['score']) >= 0.1;
            if ($opened || $closed || $changed || $levelChanged || $scoreChanged) {
                $rows[] = ['code' => $a['code'], 'name' => $a['name'], 'new_project' => $b['level'] === null, 'level_before' => $b['level'], 'level_after' => $a['level'],
                    'score_before' => $b['score'], 'score_after' => $a['score'], 'opened' => $opened, 'closed' => $closed, 'changed' => $changed];
            }
        }
        return ['projects' => $rows, 'unchanged' => count($after) - count($rows)];
    }

    /** Recompute everything for all projects (e.g. after as-of date change). */
    public function reprocessAll(): void
    {
        DB::transaction(function () {
            // lexicon/threshold changes must apply to data that is already stored
            EvidenceRecord::where('metric_key', 'update_message')->each(function ($e) {
                $signals = implode(',', $this->evidence->signals((string) $e->summary));
                if ($e->signals !== $signals) $e->update(['signals' => $signals]);
            });
            $this->postProcess(Project::pluck('id')->all());
        });
    }
}
