<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model {
    protected $primaryKey = 'key'; public $incrementing = false; protected $keyType = 'string'; protected $guarded = [];
    public static function read(string $key, $default = null) { return static::find($key)?->value ?? $default; }
    public static function write(string $key, $value): void { static::updateOrCreate(['key' => $key], ['value' => $value]); }
    /** As-of date for interpretation (BR-013): never the wall clock. */
    public static function asOf(): \Carbon\CarbonImmutable {
        return \Carbon\CarbonImmutable::parse(static::read('as_of', config('ontrack.as_of')));
    }
}
