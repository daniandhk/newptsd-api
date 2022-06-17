<?php

namespace App\Models;

class TestPage extends BaseModel
{
    protected $table = 'test_pages';

    protected $fillable = [
        'test_type_id',
        'number',
        'title',
        'description',
    ];

    public function test_type() {
        return $this->belongsTo(TestType::class);
    }

    public function test_questions() {
        return $this->hasMany(TestQuestion::class);
    }
}