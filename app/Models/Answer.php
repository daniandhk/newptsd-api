<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends BaseModel
{
    use SoftDeletes;
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
