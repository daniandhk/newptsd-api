<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestQuestion extends BaseModel
{
    use SoftDeletes, CascadeSoftDeletes;
    protected $table = 'test_questions';
    
    protected $cascadeDeletes = ['test_answers', 'answers'];

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
