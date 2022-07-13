<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

class Test extends BaseModel
{
    use SoftDeletes, CascadeSoftDeletes;
    protected $table = 'tests';

    protected $cascadeDeletes = ['answers'];

    protected $fillable = [
        'patient_id',
        'test_type_id',
        'index'
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($test) {
            $test->score = 0;
            $test_type = TestType::find($test->test_type_id);
            $test->next_date = Carbon::now('Asia/Jakarta')->addDays($test_type->delay_days);
        });
    }

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function test_type() {
        return $this->belongsTo(TestType::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }

    public function scopeFiltered($query, $patient_id) {
        return $query->where('patient_id', '=', $patient_id)->orderBy('created_at', 'desc')->first();
    }
}
