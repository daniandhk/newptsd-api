<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Psychologist extends Model
{
    protected $table = 'psychologists';

    protected $fillable = [
        'user_id',
        'full_name',
        'speciality',
        'datebirth',
        'graduation_university',
        'graduation_year',
        'city',
        'province',
        'str_number',
        'image',
    ];

    protected $appends = ['online_schedule'];

    public function getOnlineScheduleAttribute() {
        $data['is_online'] = false;
        $data['schedule'] = null;

        setlocale (LC_TIME, 'id_ID');
        $now = Carbon::now('Asia/Jakarta')->translatedFormat('l');
        $chatSchedules = $this->chatSchedule->toArray();
        $idx_today = array_search($now, array_column($chatSchedules, 'day'));
        if(is_int($idx_today)){
            $date = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            $str_now = Carbon::now('Asia/Jakarta')->format('Y-m-d H:m:s');
            $str_start = $date." ".$chatSchedules[$idx_today]['time_start'];
            $str_end = $date." ".$chatSchedules[$idx_today]['time_end'];

            if($str_now >= $str_start && $str_now <= $str_end){
                $data['schedule'] = $chatSchedules[$idx_today];
                $data['is_online'] = true;
                return $data;
            }
            else if($str_now < $str_start){
                $data['schedule'] = $chatSchedules[$idx_today];
                return $data;
            }
        }
        
        switch ($now) {
            case "Senin":
                $idx_today = 0;
                break;
            case "Selasa":
                $idx_today = 1;
                break;
            case "Rabu":
                $idx_today = 2;
                break;
            case "Kamis":
                $idx_today = 3;
                break;
            case "Jumat":
                $idx_today = 4;
                break;
            case "Sabtu":
                $idx_today = 5;
                break;
            case "Minggu":
                $idx_today = 6;
                break;
            default:
                $idx_today = null;
        }

        if($idx_today){
            $getNextDays = array_filter(
                $chatSchedules,
                function ($s) use($idx_today) {
                    return ($s['index_day'] > $idx_today);
                }
            );
            if(sizeof($getNextDays) > 0){
                $data['schedule'] = reset($getNextDays);
            }
            else{
                $getNextWeeks = array_filter(
                    $chatSchedules,
                    function ($s) use($idx_today) {
                        return ($s['index_day'] < $idx_today);
                    }
                );
                if(sizeof($getNextWeeks) > 0){
                    $data['schedule'] = reset($getNextWeeks);
                }
            }
        }
    
        return $data;
    }

    public function relation() {
        return $this->hasMany(Relation::class);
    }

    public function chatSchedule() {
        return $this->hasMany(ChatSchedule::class)->orderBy('index_day', 'ASC');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
