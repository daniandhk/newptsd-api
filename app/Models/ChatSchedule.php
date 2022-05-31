<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSchedule extends Model
{
    protected $table = "chat_schedules";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'psychologist_id',
        'day',
        'index_day',
        'time_start',
        'time_end',
    ];
    
    public function user()
    {
        return $this->belongsTo(Psychologists::class);
    }
}
