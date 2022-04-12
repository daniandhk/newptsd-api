<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    protected $table = 'relations';

    protected $fillable = [
        'relation_id',
        'patient_id',
        'psychologist_id',
        'patient_user_id',
        'psychologist_user_id',
        'status_test',
        'status_chat',
    ];

    public function consult() {
        return $this->hasOne(Consult::class);
    }

    public function psychologist() {
        return $this->belongsTo(Psychologist::class);
    }

    public function patient() {
        return $this->belongsTo(Patient::class);
    }
}
