<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestType extends Model
{
    protected $table = 'test_types';

    protected $fillable = [
        'type',
        'name',
        'total_score',
        'delay_days',
        'description'
    ];

    public function tests() {
        return $this->hasMany(Test::class);
    }
}
