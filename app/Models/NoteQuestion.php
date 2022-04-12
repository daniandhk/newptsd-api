<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteQuestion extends Model
{
    protected $table = 'note_questions';

    protected $fillable = [
        'consult_id',
        'question_text',
    ];

    public function consult() {
        return $this->belongsTo(Consult::class);
    }

    public function note_answer() {
        return $this->hasMany(NoteAnswer::class);
    }
}
