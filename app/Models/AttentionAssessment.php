<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AttentionAssessment extends Model {
    protected $guarded = [];
    protected $casts = ['criteria' => 'array', 'as_of' => 'datetime', 'is_current' => 'boolean', 'score' => 'float', 'confidence' => 'float'];
    public function project() { return $this->belongsTo(Project::class); }
}
