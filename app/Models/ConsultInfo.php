<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultInfo extends Model
{
    protected $table = 'consult_infos';

    protected $fillable = [
        'consult_id',
        'videocall_link',
        'videocall_date',
    ];

    public function consult() {
        return $this->belongsTo(Consult::class);
    }
}
