<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestType extends BaseModel
{
    use SoftDeletes, CascadeSoftDeletes;
    protected $table = 'test_types';

    protected $cascadeDeletes = ['tests', 'test_pages'];

    protected $fillable = [
        'type',
        'name',
        'delay_days',
        'description',
        'total_page',
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
        return $query->with(["tests" => function($query) use($patient_id) {
            $query->where('patient_id', $patient_id)->orderBy('created_at', 'desc');
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
