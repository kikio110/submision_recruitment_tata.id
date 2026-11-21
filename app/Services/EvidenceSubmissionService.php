<?php
namespace App\Services;

use App\Models\{EvidenceSubmission, FollowUp, FollowUpHistory, Project, Setting, User};
use App\Support\CurrentUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * PIC/PM may submit an operational observation, but it only becomes operational evidence after a
 * Director approves it. Pending/rejected submissions never reach interpretation or verification.
 */
class EvidenceSubmissionService
{
    private const NUMERIC_KINDS = ['quantity', 'pending', 'quality', 'stage', 'target', 'usage'];

    public function __construct(private DataImportService $import) {}

    /** Metrics a submission may report (label by key). */
    public static function metrics(): array
    {
        $m = ['scanner_status' => 'Status scanner (perlu subjek, mis. SCN-A)'];
        foreach (config('ontrack.metrics') as $k => $c) $m[$k] = $c['label'];
        return $m;
    }

    public static function isNumeric(string $metric): bool { return in_array(config("ontrack.metrics.$metric.kind"), self::NUMERIC_KINDS, true); }

    public function canSubmit(?User $u, Project $p): bool { return app(ProjectReportService::class)->canReport($u, $p); }

    public function submit(Project $p, array $d): EvidenceSubmission
    {
        $u = CurrentUser::get();
        $value = self::isNumeric($d['metric_key']) ? (string) (float) $d['observed_value'] : strtoupper(str_replace(' ', '_', trim($d['observed_value'])));
        $s = EvidenceSubmission::create(['project_id' => $p->id, 'follow_up_id' => $d['follow_up_id'] ?? null, 'metric_key' => $d['metric_key'],
            'subject_key' => $d['metric_key'] === 'scanner_status' ? trim($d['subject_key']) : '', 'observed_value' => $value, 'unit' => $d['unit'] ?? null,
            'observed_at' => $d['observed_at'], 'source_system' => $d['source_system'], 'source_note' => $d['source_note'] ?? null, 'status' => 'PENDING', 'submitted_by' => $u?->id]);
        $this->trail($s, 'EVIDENCE_SUBMITTED', "Bukti diajukan: {$s->metricLabel()} = {$s->observed_value} ({$s->source_system}).");
        ActivityLogService::log('EVIDENCE_SUBMITTED', "Bukti operasional diajukan untuk {$p->project_code}: {$s->metricLabel()} = {$s->observed_value}.", $p->id, $s->follow_up_id);
        if ($u?->isDirector()) $this->approve($s, 'Diinput langsung oleh Director.');
        return $s->fresh();
    }

    public function approve(EvidenceSubmission $s, ?string $note = null): EvidenceSubmission
    {
        $this->assertPending($s);
        $reviewer = CurrentUser::get();
        $p = $s->project;
        $payload = ['submission_id' => $s->id, 'submitted_by' => $s->submitter?->name ?? 'tidak diketahui', 'approved_by' => $reviewer?->name ?? 'System', 'source_note' => $s->source_note];
        if ($s->metric_key === 'scanner_status') $payload['scanners'] = [['scanner_id' => $s->subject_key, 'status' => $s->observed_value]];
        else { $payload[$s->metric_key] = self::isNumeric($s->metric_key) ? (float) $s->observed_value : $s->observed_value; if ($s->unit) $payload['unit'] = $s->unit; }
        $row = ['source_record_id' => 'SUB-' . $s->id, 'project_id' => $p->project_code, 'source_system' => $s->source_system, 'record_type' => 'submitted_observation',
            'timestamp' => $s->observed_at->format('Y-m-d\TH:i:s')] + $payload;
        $b = $this->import->importRows(DataValidationService::KIND_PRODUCTION, [$row], "Bukti diajukan #{$s->id} ({$p->project_code})");
        if ($b->invalid_records > 0 || in_array($b->status, ['FAILED', 'REJECTED'])) {
            throw ValidationException::withMessages(['review_note' => 'Bukti tidak dapat diproses: ' . ($b->errors()->where('severity', 'ERROR')->first()?->message ?? $b->message)]);
        }
        $s->update(['status' => 'APPROVED', 'reviewed_by' => $reviewer?->id, 'reviewed_at' => now(), 'review_note' => $note, 'import_batch_id' => $b->id]);
        $this->trail($s, 'EVIDENCE_REVIEWED', 'Bukti DISETUJUI dan dihitung sebagai evidence operasional.' . ($note ? " Catatan: $note" : ''));
        ActivityLogService::log('EVIDENCE_APPROVED', "Bukti #{$s->id} untuk {$p->project_code} disetujui oleh " . ($reviewer?->name ?? 'System') . '.', $p->id, $s->follow_up_id, $b->id);
        return $s->fresh();
    }

    public function reject(EvidenceSubmission $s, string $note): EvidenceSubmission
    {
        $this->assertPending($s);
        DB::transaction(function () use ($s, $note) {
            $s->update(['status' => 'REJECTED', 'reviewed_by' => CurrentUser::id(), 'reviewed_at' => now(), 'review_note' => $note]);
            $this->trail($s, 'EVIDENCE_REVIEWED', "Bukti DITOLAK dan tidak dihitung sebagai evidence. Catatan: $note");
            ActivityLogService::log('EVIDENCE_REJECTED', "Bukti #{$s->id} untuk {$s->project->project_code} ditolak: $note", $s->project_id, $s->follow_up_id);
        });
        return $s->fresh();
    }

    private function assertPending(EvidenceSubmission $s): void
    {
        if ($s->status !== 'PENDING') throw ValidationException::withMessages(['review_note' => 'Pengajuan ini sudah ditinjau.']);
    }

    private function trail(EvidenceSubmission $s, string $event, string $note): void
    {
        if (! $s->follow_up_id) return;
        FollowUpHistory::create(['follow_up_id' => $s->follow_up_id, 'event' => $event, 'to_value' => $s->status, 'note' => $note, 'actor_id' => CurrentUser::id(), 'actor_name' => CurrentUser::name()]);
    }

    public static function pendingCount(): int { return EvidenceSubmission::where('status', 'PENDING')->count(); }
}
