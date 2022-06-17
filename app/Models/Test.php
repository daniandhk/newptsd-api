<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Test extends BaseModel
{
    protected $table = 'tests';

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
            $test->is_finished = false;
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
}
