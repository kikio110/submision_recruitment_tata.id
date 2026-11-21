<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FollowUp extends Model {
    public const STATUSES = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CANCELLED'];
    public const PRIORITIES = ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'];
    protected $guarded = [];
    protected $casts = ['due_date' => 'date', 'resolved_at' => 'datetime'];
    public function project() { return $this->belongsTo(Project::class); }
    public function finding() { return $this->belongsTo(Finding::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }
    public function histories() { return $this->hasMany(FollowUpHistory::class)->orderByDesc('created_at')->orderByDesc('id'); }
    public function verifications() { return $this->hasMany(IssueVerification::class)->latest('created_at')->latest('id'); }
    public function evidenceSubmissions() { return $this->hasMany(EvidenceSubmission::class)->latest('id'); }
    public function isOverdue(): bool {
        return $this->due_date && in_array($this->status, ['OPEN', 'IN_PROGRESS']) && $this->due_date->lt(Setting::asOf()->startOfDay());
    }
}
