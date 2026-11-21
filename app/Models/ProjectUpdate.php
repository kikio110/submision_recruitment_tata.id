<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProjectUpdate extends Model {
    protected $guarded = [];
    protected $casts = ['occurred_at' => 'datetime', 'is_current' => 'boolean'];
    public function project() { return $this->belongsTo(Project::class); }
}
