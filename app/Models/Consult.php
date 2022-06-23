<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consult extends BaseModel
{
    protected $table = 'consults';

    protected $fillable = [
        'relation_id',
        'consult_index',
        'last_date',
        'next_date',
        'is_finished'
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($consult) {
            $consult->is_finished = false;
        });
    }

    public function relation() {
        return $this->belongsTo(Relation::class);
    }

    public function consult_info() {
        return $this->hasOne(ConsultInfo::class);
    }

    public function note_questions() {
        return $this->hasMany(NoteQuestion::class);
    }
}
