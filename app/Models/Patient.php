<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'datebirth',
        'city',
        'province',
        'have_relation'
    ];

    public function test() {
        return $this->hasMany(Test::class);
    }
}
