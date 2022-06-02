<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends BaseModel
{
    protected $table = 'guardians';

    protected $fillable = [
        'patient_id',
        'full_name',
        'status',
        'phone',
        'is_available'
    ];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }
}
