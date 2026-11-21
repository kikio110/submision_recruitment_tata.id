<?php
namespace App\Services;

use App\Models\{Finding, FollowUp, FollowUpHistory, User};
use App\Support\CurrentUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FollowUpService
{
    /** Allowed status transitions (FR-008). */
    public const TRANSITIONS = [
        'OPEN' => ['IN_PROGRESS', 'RESOLVED', 'CANCELLED'],
        'IN_PROGRESS' => ['OPEN', 'RESOLVED', 'CANCELLED'],
        'RESOLVED' => ['OPEN', 'IN_PROGRESS'],
        'CANCELLED' => ['OPEN'],
    ];

    public function __construct(private IssueVerificationService $verification) {}

    private function history(FollowUp $f, string $event, ?string $from = null, ?string $to = null, ?string $note = null): void
    {
        FollowUpHistory::create(['follow_up_id' => $f->id, 'event' => $event, 'from_value' => $from, 'to_value' => $to, 'note' => $note,
            'actor_id' => CurrentUser::id(), 'actor_name' => CurrentUser::name()]);
    }

    public function create(array $d): FollowUp
    {
        if (! empty($d['finding_id']) && ! Finding::where('id', $d['finding_id'])->where('project_id', $d['project_id'])->exists())
            throw ValidationException::withMessages(['finding_id' => 'Temuan tidak termasuk dalam proyek yang dipilih.']);
        return DB::transaction(function () use ($d) {
            $f = FollowUp::create(['project_id' => $d['project_id'], 'finding_id' => $d['finding_id'] ?? null, 'title' => $d['title'], 'description' => $d['description'] ?? null,
                'instructions' => $d['instructions'] ?? null, 'assigned_pic' => $d['assigned_pic'], 'priority' => $d['priority'] ?? 'MEDIUM', 'due_date' => $d['due_date'] ?? null,
                'status' => 'OPEN', 'created_by' => CurrentUser::id(), 'updated_by' => CurrentUser::id()]);
            $this->history($f, 'CREATED', null, 'OPEN', "Dibuat dan ditugaskan kepada {$f->assigned_pic} (prioritas {$f->priority}).");
            ActivityLogService::log('FOLLOW_UP_CREATED', "Follow-up \"{$f->title}\" dibuat dan ditugaskan ke {$f->assigned_pic}.", $f->project_id, $f->id);
            return $f;
        });
    }

    /** Update details; reassignment is recorded separately. Status changes go through changeStatus(). */
    public function update(FollowUp $f, array $d): FollowUp
    {
        return DB::transaction(function () use ($f, $d) {
            $oldPic = $f->assigned_pic; $changes = [];
            foreach (['title', 'description', 'instructions', 'priority', 'due_date'] as $k) {
                if (array_key_exists($k, $d) && (string) ($f->{$k} instanceof \DateTimeInterface ? $f->{$k}->toDateString() : $f->{$k}) !== (string) $d[$k]) $changes[] = $k;
            }
            $f->fill(array_intersect_key($d, array_flip(['title', 'description', 'instructions', 'priority', 'due_date', 'assigned_pic'])));
            $f->updated_by = CurrentUser::id(); $f->save();
            if ($changes) { $this->history($f, 'UPDATED', null, null, 'Diubah: ' . implode(', ', $changes)); ActivityLogService::log('FOLLOW_UP_UPDATED', "Follow-up \"{$f->title}\" diubah (" . implode(', ', $changes) . ').', $f->project_id, $f->id); }
            if (isset($d['assigned_pic']) && $d['assigned_pic'] !== $oldPic) {
                $this->history($f, 'REASSIGNED', $oldPic, $d['assigned_pic']);
                ActivityLogService::log('FOLLOW_UP_ASSIGNED', "Follow-up \"{$f->title}\" ditugaskan ulang dari $oldPic ke {$d['assigned_pic']}.", $f->project_id, $f->id);
            }
            return $f;
        });
    }

    public function addNote(FollowUp $f, string $note): void
    {
        DB::transaction(function () use ($f, $note) {
            $this->history($f, 'NOTE_ADDED', null, null, $note);
            $f->update(['updated_by' => CurrentUser::id()]);
            ActivityLogService::log('FOLLOW_UP_NOTE', "Catatan progres pada \"{$f->title}\": " . mb_substr($note, 0, 140), $f->project_id, $f->id);
        });
    }

    public function changeStatus(FollowUp $f, string $to, ?string $note = null): FollowUp
    {
        $from = $f->status;
        if (! in_array($to, FollowUp::STATUSES, true)) throw ValidationException::withMessages(['status' => 'Status tidak dikenal.']);
        if (! in_array($to, self::TRANSITIONS[$from] ?? [], true)) throw ValidationException::withMessages(['status' => "Transisi $from → $to tidak diizinkan."]);
        return DB::transaction(function () use ($f, $from, $to, $note) {
            $f->update(['status' => $to, 'updated_by' => CurrentUser::id(), 'resolved_at' => $to === 'RESOLVED' ? now() : null]);
            $this->history($f, 'STATUS_CHANGED', $from, $to, $note);
            ActivityLogService::log('FOLLOW_UP_STATUS', "Status follow-up \"{$f->title}\": $from → $to.", $f->project_id, $f->id);
            if ($to === 'RESOLVED' && $f->finding) {
                // RESOLVED only means the *action* is done; the issue is judged by evidence (BR-006).
                $f->finding->update(['verification_status' => 'PENDING_VERIFICATION']);
                $this->verification->verify($f->finding->fresh(), $f, 'FOLLOW_UP_RESOLVED');
            } elseif ($to !== 'RESOLVED' && $from === 'RESOLVED' && $f->finding && $f->finding->verification_status === 'PENDING_VERIFICATION') {
                $f->finding->update(['verification_status' => 'OPEN']);
            }
            return $f->fresh();
        });
    }

    public function canModify(?User $u, FollowUp $f): bool
    {
        if (! $u) return false;
        return $u->isDirector() || ($u->pic_name && strcasecmp($u->pic_name, $f->assigned_pic) === 0);
    }
}
