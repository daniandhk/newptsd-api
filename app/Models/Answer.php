<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends BaseModel
{
    protected $table = 'answers';

    protected $fillable = [
        'test_id',
        'test_answer_id',
        'test_question_id',
        'text',
    ];

    public function test() {
        return $this->belongsTo(Test::class);
    }

    public function test_answer() {
        return $this->belongsTo(TestAnswer::class);
    }

    public function test_question() {
        return $this->belongsTo(TestQuestion::class);
    }
}
