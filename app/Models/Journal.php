<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends BaseModel
{
    protected $table = 'journals';

    protected $fillable = [
        'patient_id',
        'text',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
