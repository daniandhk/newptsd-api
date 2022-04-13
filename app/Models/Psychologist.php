<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Psychologist extends Model
{
    protected $table = 'psychologists';

    protected $fillable = [
        'user_id',
        'full_name',
        'speciality',
        'datebirth',
        'firstyear_experience',
        'workplace',
        'str_number',
    ];

    public function relation() {
        return $this->hasMany(Relation::class);
    }

    public function chatSchedule() {
        return $this->hasMany(ChatSchedule::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}