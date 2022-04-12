<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consult extends Model
{
    protected $table = 'consults';

    protected $fillable = [
        'relation_id',
        'consult_index',
        'last_date',
        'next_date',
    ];

    public function relation() {
        return $this->belongsTo(Relation::class);
    }

    public function consult_info() {
        return $this->hasOne(ConsultInfo::class);
    }

    public function note_question() {
        return $this->hasMany(NoteQuestion::class);
    }
}
