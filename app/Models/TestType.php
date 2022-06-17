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
        'total_page'
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($test_type) {
            if(!$test_type->total_score){
                $test_type->total_score = 0;
            }
        });
    }

    public function tests() {
        return $this->hasMany(Test::class);
    }

    public function test_pages() {
        return $this->hasMany(TestPage::class)->orderBy('number');
    }
}
