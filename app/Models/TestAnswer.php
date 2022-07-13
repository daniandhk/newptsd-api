<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestAnswer extends BaseModel
{
    use SoftDeletes, CascadeSoftDeletes;
    protected $table = 'test_answers';

    protected $cascadeDeletes = ['answers'];

    protected $fillable = [
        'test_question_id',
        'text',
        'description',
        'weight',
        'is_essay'
    ];

    public function test_question() {
        return $this->belongsTo(TestQuestion::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }
}
