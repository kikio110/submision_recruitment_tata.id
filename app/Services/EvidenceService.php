<?php
namespace App\Services;

use App\Models\{EvidenceRecord, ProductionRecord, Project, ProjectUpdate};
use Carbon\CarbonImmutable;

/**
 * Converts source records into standardised evidence (SRS FR-004, BR-003/005).
 * Evidence is append-only: superseded values stay, flagged is_current=false.
 */
class EvidenceService
{
    public function signals(string $message): array
    {
        $m = mb_strtolower($message);
        $out = [];
        foreach (config('ontrack.lexicon') as $signal => $needles) {
            foreach ($needles as $n) if (str_contains($m, $n)) { $out[] = $signal; break; }
        }
        return $out;
    }

    private function make(array $a): ?EvidenceRecord
    {
        $fp = sha1(implode('|', [$a['origin_table'], $a['origin_id'], $a['revision'] ?? 1, $a['metric_key'], $a['subject_key'] ?? '']));
        return EvidenceRecord::firstOrCreate(['fingerprint' => $fp], $a + ['subject_key' => '']);
    }

    public function ingestMaster(Project $p, int $revision, string $asOf): void
    {
        $at = $p->last_reported_update_at?->toDateTimeString() ?? $asOf;
        $base = ['project_id' => $p->id, 'evidence_class' => 'REPORTED', 'category' => 'REPORT', 'origin_table' => 'projects',
            'origin_id' => $p->id, 'source_ref' => "MASTER:{$p->project_code}", 'source_system' => 'project_master',
            'source_label' => 'Project Master' . ($p->pic ? " (PIC {$p->pic})" : ''), 'revision' => $revision, 'observed_at' => $at];
        if ($p->reported_progress_pct !== null) $this->make($base + ['metric_key' => 'reported_progress_pct', 'observed_value' => (string) $p->reported_progress_pct,
            'numeric_value' => $p->reported_progress_pct, 'unit' => '%', 'summary' => "Progress dilaporkan {$p->reported_progress_pct}% pada project master."]);
        if ($p->reported_actual !== null) $this->make($base + ['metric_key' => 'reported_actual', 'observed_value' => (string) $p->reported_actual,
            'numeric_value' => $p->reported_actual, 'unit' => $p->target_unit, 'summary' => "Aktual dilaporkan {$p->reported_actual} {$p->target_unit} dari target {$p->target}."]);
        if ($p->reported_status) $this->make($base + ['metric_key' => 'reported_status', 'observed_value' => $p->reported_status,
            'summary' => "Status dilaporkan: {$p->reported_status}."]);
    }

    public function ingestUpdate(ProjectUpdate $u): void
    {
        $this->make(['project_id' => $u->project_id, 'evidence_class' => 'REPORTED', 'category' => 'REPORT', 'origin_table' => 'project_updates',
            'origin_id' => $u->id, 'source_ref' => $u->update_code, 'source_system' => $u->source_type, 'source_label' => $u->source_name,
            'revision' => $u->revision, 'observed_at' => $u->occurred_at, 'metric_key' => 'update_message', 'subject_key' => $u->update_code,
            'observed_value' => mb_substr($u->message, 0, 250), 'summary' => $u->message, 'signals' => implode(',', $this->signals($u->message))]);
    }

    public function ingestProduction(ProductionRecord $r): void
    {
        $catalog = config('ontrack.metrics');
        $meta = array_flip(config('ontrack.meta_fields'));
        $p = $r->payload;
        $base = ['project_id' => $r->project_id, 'evidence_class' => 'OPERATIONAL', 'origin_table' => 'production_records', 'origin_id' => $r->id,
            'source_ref' => $r->source_record_id, 'source_system' => $r->source_system, 'source_label' => "{$r->source_system} / {$r->record_type}",
            'revision' => $r->revision, 'observed_at' => $r->observed_at];
        $unit = $p['unit'] ?? null;
        if (! empty($p['submitted_by'])) { // evidence submitted by a PIC and approved by a Director keeps that trail
            $base['source_label'] = mb_substr("{$r->source_system} — diajukan {$p['submitted_by']}, disetujui {$p['approved_by']}" . (! empty($p['source_note']) ? ' · ' . $p['source_note'] : ''), 0, 250);
        }
        foreach ($p as $k => $v) {
            if (isset($meta[$k]) || is_array($v)) continue;
            $c = $catalog[$k] ?? ['kind' => is_numeric($v) ? 'quantity' : 'status', 'category' => 'OTHER', 'label' => $k];
            $num = is_numeric($v);
            $this->make($base + ['category' => $c['category'], 'metric_key' => $k, 'observed_value' => $v === null ? null : (string) $v,
                'numeric_value' => $num ? (float) $v : null, 'unit' => $unit ?? (str_contains($k, 'endpoints') ? 'endpoints' : null),
                'summary' => $c['label'] . ': ' . $v . ($unit ? " $unit" : '')]);
        }
        if ($v = $p['connection_status'] ?? null) {
            // keep "never connected" explicit: null is a recorded value, not a missing one
            if (array_key_exists('last_successful_connection', $p) && $p['last_successful_connection'] === null) {
                EvidenceRecord::where('fingerprint', sha1("production_records|{$r->id}|{$r->revision}|connection_status|"))
                    ->update(['summary' => "Status koneksi {$p['environment']}: $v (belum pernah ada koneksi berhasil)."]);
            }
        }
        foreach ($p['scanners'] ?? [] as $s) {
            $this->make($base + ['category' => 'EQUIPMENT', 'metric_key' => 'scanner_status', 'subject_key' => $s['scanner_id'],
                'observed_value' => $s['status'], 'summary' => "Scanner {$s['scanner_id']}: {$s['status']}"]);
        }
    }

    /** Recompute current/stale/duplicate/conflict flags for one project. */
    public function refreshProject(Project $project, CarbonImmutable $asOf): void
    {
        $rows = EvidenceRecord::where('project_id', $project->id)->orderBy('observed_at')->orderBy('revision')->orderBy('id')->get();
        $origin = ['project_updates' => ProjectUpdate::where('project_id', $project->id)->pluck('is_current', 'id'),
            'production_records' => ProductionRecord::where('project_id', $project->id)->pluck('is_current', 'id')];
        $latest = []; $oc = [];
        foreach ($rows as $e) {
            $originCurrent = isset($origin[$e->origin_table]) ? (bool) ($origin[$e->origin_table][$e->origin_id] ?? true) : true;
            $oc[$e->id] = $originCurrent;
            if ($originCurrent) $latest[$e->evidence_class . '|' . $e->metric_key . '|' . $e->subject_key] = $e->id;
        }
        $staleLimit = $asOf->subHours(config('ontrack.thresholds.stale_hours'));
        $seen = [];
        $groups = [];
        $byMetric = $rows->groupBy(fn ($e) => $e->evidence_class . '|' . $e->metric_key . '|' . $e->subject_key);
        $window = config('ontrack.thresholds.conflict_window_hours');
        foreach ($byMetric as $key => $list) {
            if ($list->first()->evidence_class !== 'OPERATIONAL' || $list->count() < 2) continue;
            $arr = $list->values();
            for ($i = 0; $i < $arr->count(); $i++) for ($j = $i + 1; $j < $arr->count(); $j++) {
                $a = $arr[$i]; $b = $arr[$j];
                if ($a->source_ref === $b->source_ref || $a->observed_value === $b->observed_value) continue;
                $dt = abs($a->observed_at->diffInHours($b->observed_at, true));
                $same = $a->observed_at->equalTo($b->observed_at);
                if ($same || ($a->source_system !== $b->source_system && $dt <= $window)) {
                    $g = substr(sha1($project->id . '|' . $key . '|' . $a->observed_at->format('Y-m-d H')), 0, 20);
                    $groups[$a->id] = $groups[$b->id] = $g;
                }
            }
        }
        foreach ($rows as $e) {
            $dupKey = implode('|', [$e->evidence_class, $e->metric_key, $e->subject_key, $e->observed_at, $e->observed_value]);
            $isDup = isset($seen[$dupKey]) && $seen[$dupKey] !== $e->source_ref;
            $seen[$dupKey] ??= $e->source_ref;
            $current = $oc[$e->id] && ($latest[$e->evidence_class . '|' . $e->metric_key . '|' . $e->subject_key] ?? null) === $e->id;
            if ($e->metric_key === 'update_message') $current = $oc[$e->id];
            $stale = $current && $e->metric_key !== 'update_message' && $e->observed_at->lt($staleLimit);
            $e->forceFill(['is_current' => $current, 'is_stale' => $stale, 'is_duplicate' => $isDup, 'conflict_group' => $groups[$e->id] ?? null]);
            if ($e->isDirty()) $e->save();
        }
    }
}
