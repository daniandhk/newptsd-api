<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterToken extends Model
{
    protected $table = 'register_tokens';

    protected $fillable = [
        'token',
        'expired_at'
    ];
}
