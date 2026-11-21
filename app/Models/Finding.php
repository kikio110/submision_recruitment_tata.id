<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Finding extends Model {
    protected $guarded = [];
    protected $casts = ['baseline_at' => 'datetime', 'detected_at' => 'datetime', 'last_detected_at' => 'datetime', 'is_active' => 'boolean'];
    public function project() { return $this->belongsTo(Project::class); }
    public function evidence() { return $this->belongsToMany(EvidenceRecord::class, 'finding_evidence', 'finding_id', 'evidence_id')->withPivot('role')->orderBy('observed_at'); }
    public function followUps() { return $this->hasMany(FollowUp::class); }
    public function verifications() { return $this->hasMany(IssueVerification::class)->latest('created_at')->latest('id'); }
    public function metrics(): array { return array_filter(explode(',', (string) $this->relevant_metrics)); }
}
