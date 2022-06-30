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
        'videocall_date',
        'videocall_link',
    ];

    protected $appends = ['total_note_questions'];

    public function getTotalNoteQuestionsAttribute() {
        return $this->note_questions()->count();
    }

    public function relation() {
        return $this->belongsTo(Relation::class);
    }

    public function note_questions() {
        return $this->hasMany(NoteQuestion::class);
    }
}
