<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSchedule extends BaseModel
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
        'time_start',
        'time_end',
    ];

    public static function boot() {
        parent::boot();
        static::creating(function($chat_schedule) {
            switch ($chat_schedule->day) {
                case "Senin":
                    $chat_schedule->index_day = 0;
                    break;
                case "Selasa":
                    $chat_schedule->index_day = 1;
                    break;
                case "Rabu":
                    $chat_schedule->index_day = 2;
                    break;
                case "Kamis":
                    $chat_schedule->index_day = 3;
                    break;
                case "Jumat":
                    $chat_schedule->index_day = 4;
                    break;
                case "Sabtu":
                    $chat_schedule->index_day = 5;
                    break;
                case "Minggu":
                    $chat_schedule->index_day = 6;
                    break;
                default:
                    $chat_schedule->index_day = null;
            }
        });
    }
    
    public function user() {
        return $this->belongsTo(Psychologists::class);
    }
}
