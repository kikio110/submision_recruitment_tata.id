<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DataImportBatch extends Model {
    protected $guarded = [];
    protected $casts = ['as_of' => 'datetime', 'started_at' => 'datetime', 'finished_at' => 'datetime', 'impact' => 'array'];
    public function errors() { return $this->hasMany(DataValidationError::class, 'batch_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
