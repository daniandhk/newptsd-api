<?php

namespace App\Models;

class TestAnswer extends BaseModel
{
    protected $table = 'test_answers';

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

    public function answer() {
        return $this->hasMany(Answer::class);
    }
}
