<?php
namespace App\Services;

use App\Models\{EvidenceRecord, Finding, FollowUp, FollowUpHistory, IssueVerification, Setting};
use App\Support\CurrentUser;

/**
 * Evidence-based closure (brief section 11, BR-006/007/008).
 * Independent of follow-up status: a RESOLVED follow-up never makes an issue VERIFIED by itself.
 */
class IssueVerificationService
{
    public function __construct(private ProjectContextService $contexts, private InterpretationService $interpretation) {}

    public function verify(Finding $finding, ?FollowUp $followUp = null, string $trigger = 'MANUAL'): IssueVerification
    {
        $ctx = $this->contexts->forProject($finding->project_id);
        $asOf = $ctx->asOf; $limit = $asOf->subHours(config('ontrack.thresholds.stale_hours'));
        $baseline = $finding->baseline_at;
        $metrics = $finding->metrics();
        $subject = str_contains((string) $finding->subject_key, ':') ? explode(':', $finding->subject_key, 2)[1] : null;

        $previous = $finding->evidence()->where('evidence_class', 'OPERATIONAL')->orderByDesc('observed_at')->first()
            ?? $finding->evidence()->orderByDesc('observed_at')->first();

        $candidates = $ctx->operational()->filter(function (EvidenceRecord $e) use ($metrics, $subject, $baseline) {
            if ($baseline && $e->observed_at->lte($baseline)) return false;
            if (in_array('*', $metrics, true) || ! $metrics) return true;
            if (! in_array($e->metric_key, $metrics, true)) return false;
            return $subject === null || $subject === '' || $e->subject_key === '' || $e->subject_key === $subject;
        })->sortBy('observed_at')->values();
        $latest = $candidates->last();

        if (! $latest) {
            [$result, $why] = ['UNVERIFIED', 'Belum ada evidence operasional yang lebih baru dari baseline temuan' . ($baseline ? ' (' . $baseline->format('d M Y H:i') . ')' : '') . '. Status follow-up atau pesan PM tidak cukup untuk memverifikasi pemulihan; kondisi dibiarkan UNVERIFIED.'];
        } elseif ($candidates->contains(fn ($e) => $e->conflict_group)) {
            [$result, $why] = ['UNVERIFIED', 'Evidence terbaru saling bertentangan antar sumber, sehingga pemulihan tidak dapat dipastikan. Perlu klarifikasi sumber.'];
        } elseif ($latest->observed_at->lt($limit)) {
            [$result, $why] = ['UNVERIFIED', "Evidence terbaru ({$latest->source_ref}, {$latest->observed_at->format('d M Y H:i')}) sudah usang terhadap tanggal acuan {$asOf->format('d M Y H:i')}."];
        } else {
            $still = $this->interpretation->evaluate($ctx)[$finding->fingerprint] ?? null;
            if ($still) {
                [$result, $why] = ['REOPENED', "Evidence terbaru ({$latest->source_ref}, {$latest->observed_at->format('d M Y H:i')}) menunjukkan kondisi asal masih terdeteksi: {$still['description']}"];
            } else {
                [$result, $why] = ['VERIFIED', "Evidence operasional terbaru ({$latest->source_ref}, {$latest->observed_at->format('d M Y H:i')}: {$latest->summary}) lebih baru dari baseline temuan, cukup segar, tidak bertentangan, dan aturan asal tidak lagi mendeteksi kondisi tersebut."];
            }
        }

        $v = IssueVerification::create(['finding_id' => $finding->id, 'follow_up_id' => $followUp?->id, 'result' => $result, 'explanation' => $why,
            'previous_evidence_id' => $previous?->id, 'latest_evidence_id' => $latest?->id, 'trigger' => $trigger, 'as_of' => $asOf, 'triggered_by' => CurrentUser::id()]);
        $finding->update(['verification_status' => $result]);

        foreach ($followUp ? [$followUp] : $finding->followUps()->whereIn('status', ['RESOLVED'])->get() as $fu) {
            FollowUpHistory::create(['follow_up_id' => $fu->id, 'event' => 'VERIFICATION', 'to_value' => $result, 'note' => $why,
                'actor_id' => CurrentUser::id(), 'actor_name' => $trigger === 'DATA_REFRESH' ? 'System' : CurrentUser::name()]);
        }
        ActivityLogService::log('ISSUE_VERIFICATION', "Verifikasi issue \"{$finding->title}\": $result ($trigger).", $finding->project_id, $followUp?->id, null, $trigger === 'DATA_REFRESH');
        return $v;
    }
}
