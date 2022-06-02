<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteAnswer extends BaseModel
{
    protected $table = 'note_answers';

    protected $fillable = [
        'note_question_id',
        'date',
        'answer_text',
    ];

    public function note_question() {
        return $this->belongsTo(NoteQuestion::class);
    }
}
