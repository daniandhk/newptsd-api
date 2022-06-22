<?php

namespace App\Models;

class Message extends BaseModel
{
    protected $table = 'messages';

    protected $fillable = ['text'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
