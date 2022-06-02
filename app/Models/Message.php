<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends BaseModel
{
    protected $guarded=[];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
