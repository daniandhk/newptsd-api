<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends BaseModel
{
    protected $table = 'patients';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'datebirth',
        'city',
        'province',
        'phone',
        'image',
    ];

    public function tests() {
        return $this->hasMany(Test::class);
    }

    public function relation() {
        return $this->hasOne(Relation::class);
    }

    public function guardian() {
        return $this->hasOne(Guardian::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
