<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EvidenceRecord extends Model {
    protected $guarded = [];
    protected $casts = ['observed_at' => 'datetime', 'numeric_value' => 'float', 'is_current' => 'boolean', 'is_stale' => 'boolean', 'is_duplicate' => 'boolean'];
    public function project() { return $this->belongsTo(Project::class); }
    public function findings() { return $this->belongsToMany(Finding::class, 'finding_evidence')->withPivot('role'); }
    public function signalList(): array { return array_filter(explode(',', (string) $this->signals)); }
    /** Human readable source: descriptive label plus the raw source_type / source_system. */
    public function sourceText(): string
    {
        $label = (string) $this->source_label; $sys = (string) $this->source_system;
        return ($label && $sys && ! str_contains($label, $sys)) ? "$label · $sys" : ($label ?: $sys);
    }
    public function isOperational(): bool { return $this->evidence_class === 'OPERATIONAL'; }
}
