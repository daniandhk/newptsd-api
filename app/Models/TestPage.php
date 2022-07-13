<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestPage extends BaseModel
{
    use SoftDeletes, CascadeSoftDeletes;
    protected $table = 'test_pages';

    protected $cascadeDeletes = ['test_questions'];

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