<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'answers';

    protected $fillable = [
        // 'patient_id',
        'test_id',
        'answer_text',
        'answer_type',
        'question_index',
    ];

    public function test() {
        return $this->belongsTo(Test::class);
    }

    // public function patient() {
    //     return $this->belongsTo(Patient::class);
    // }
}
