<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductionRecord extends Model {
    protected $guarded = [];
    protected $casts = ['observed_at' => 'datetime', 'payload' => 'array', 'is_current' => 'boolean'];
    public function project() { return $this->belongsTo(Project::class); }
}
