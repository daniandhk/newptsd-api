<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends BaseModel
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'header',
        'body',
        'avatar'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
