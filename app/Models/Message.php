<?php

namespace App\Models;

class Message extends BaseModel
{
    protected $table = 'messages';

    protected $fillable = [
        'text',
        'receiver_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function receiver() {
        return $this->belongsTo(User::class);
    }
}
