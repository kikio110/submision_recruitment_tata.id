<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class IssueVerification extends Model {
    public const UPDATED_AT = null;
    protected $guarded = [];
    protected $casts = ['created_at' => 'datetime', 'as_of' => 'datetime'];
    public function finding() { return $this->belongsTo(Finding::class); }
    public function followUp() { return $this->belongsTo(FollowUp::class); }
    public function previousEvidence() { return $this->belongsTo(EvidenceRecord::class, 'previous_evidence_id'); }
    public function latestEvidence() { return $this->belongsTo(EvidenceRecord::class, 'latest_evidence_id'); }
}
