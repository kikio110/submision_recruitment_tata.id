<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FollowUpHistory extends Model {
    public const UPDATED_AT = null;
    protected $guarded = [];
    protected $casts = ['created_at' => 'datetime'];
    public function followUp() { return $this->belongsTo(FollowUp::class); }
}
