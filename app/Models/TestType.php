<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestType extends BaseModel
{
    protected $table = 'test_types';

    protected $fillable = [
        'type',
        'name',
        'delay_days',
        'description',
        'submitter_id',
        'updater_id'
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($test_type) {
            if(!$test_type->total_score){
                $test_type->total_score = 0;
            }
        });
    }

    public function scopeFilteredTest($query, $patient_id) {
        return $query->with(["tests" => function($q) use($patient_id) {
            $q->where('tests.patient_id', '=', $patient_id)->orderBy('created_at', 'desc')->first();
        }]);
    }

    public function scopeFilteredAllTest($query, $patient_id) {
        return $query->with(["tests" => function($q) use($patient_id) {
            $q->where('tests.patient_id', '=', $patient_id)->orderBy('created_at', 'desc')->get();
        }]);
    }

    public function tests() {
        return $this->hasMany(Test::class);
    }

    public function test_pages() {
        return $this->hasMany(TestPage::class)->orderBy('number');
    }

    public function submitter() {
        return $this->belongsTo(User::class);
    }

    public function updater() {
        return $this->belongsTo(User::class);
    }
}
