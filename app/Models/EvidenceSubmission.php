<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EvidenceSubmission extends Model {
    protected $guarded = [];
    protected $casts = ['observed_at' => 'datetime', 'reviewed_at' => 'datetime'];
    public function project() { return $this->belongsTo(Project::class); }
    public function followUp() { return $this->belongsTo(FollowUp::class); }
    public function submitter() { return $this->belongsTo(User::class, 'submitted_by'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function metricLabel(): string {
        return $this->metric_key === 'scanner_status' ? 'Status scanner' : (config("ontrack.metrics.{$this->metric_key}.label") ?? $this->metric_key);
    }
}
