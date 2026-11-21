<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Project extends Model {
    protected $guarded = [];
    protected $casts = ['start_date' => 'date', 'deadline' => 'date', 'last_reported_update_at' => 'datetime',
        'target' => 'float', 'reported_actual' => 'float', 'reported_progress_pct' => 'float'];
    public function updates() { return $this->hasMany(ProjectUpdate::class)->orderBy('occurred_at')->orderBy('id'); }
    public function productionRecords() { return $this->hasMany(ProductionRecord::class)->orderBy('observed_at')->orderBy('id'); }
    public function evidence() { return $this->hasMany(EvidenceRecord::class)->orderBy('observed_at')->orderBy('id'); }
    public function findings() { return $this->hasMany(Finding::class); }
    public function activeFindings() { return $this->hasMany(Finding::class)->where('is_active', true); }
    public function followUps() { return $this->hasMany(FollowUp::class); }
    public function evidenceSubmissions() { return $this->hasMany(EvidenceSubmission::class)->latest('id'); }
    public function assessments() { return $this->hasMany(AttentionAssessment::class); }
    public function currentAssessment() { return $this->hasOne(AttentionAssessment::class)->where('is_current', true)->latestOfMany(); }
}
