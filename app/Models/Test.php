<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Test extends Model
{
    protected $table = 'tests';

    protected $fillable = [
        'patient_id',
        'next_date',
        'score',
        'videocall_link',
        'videocall_date',
        'is_finished'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function($test) {
            $test->next_date = Carbon::now('Asia/Jakarta')->addMonth();
            $test->is_finished = false;
        });
    }

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function answer() {
        return $this->hasMany(Answer::class);
    }
}
