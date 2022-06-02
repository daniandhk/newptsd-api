<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relation extends BaseModel
{
    protected $table = 'relations';

    protected $fillable = [
        'relation_id',
        'patient_id',
        'psychologist_id',
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
