<?php

namespace App\Models;

class TestQuestion extends BaseModel
{
    protected $table = 'test_questions';

    protected $fillable = [
        'test_page_id',
        'text',
        'answer_type'
    ];

    public function test_page() {
        return $this->belongsTo(TestPage::class);
    }

    public function test_answers() {
        return $this->hasMany(TestAnswer::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }
}
