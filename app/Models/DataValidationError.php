<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DataValidationError extends Model {
    protected $guarded = [];
    public function batch() { return $this->belongsTo(DataImportBatch::class, 'batch_id'); }
}
