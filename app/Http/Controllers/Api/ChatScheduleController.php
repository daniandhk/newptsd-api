<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Psychologist;
use App\Models\ChatSchedule;
use App\Models\Relation;

class ChatScheduleController extends BaseController
{
    public function index()
    {
        $chatschedules = ChatSchedule::all();
        return $this->respond($chatschedules);
    }

    public function show($id)
    {
        $chatschedule = ChatSchedule::where('psychologist_id',$id)->get();
        if(!$chatschedule) {
            return $this->errorNotFound('invalid psychologist_id');
        }
        return $this->respond($chatschedule);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'psychologist_id' => 'required',
            'schedules' => 'required',
        ]);
        if (Psychologist::find($request->psychologist_id) != null) {
            if(!is_array($request->schedules) or sizeof($request->schedules) < 1) {
                return $this->validationError();
            }
            foreach($request->schedules as $schedule) {
                if(ChatSchedule::where('psychologist_id', $request->psychologist_id)
                                ->where('day', $schedule['day'])
                                ->exists())
                {
                    ChatSchedule::where('psychologist_id', $request->psychologist_id)
                                ->where('day', $schedule['day'])
                                ->delete();
                }
                $chat_schedule = ChatSchedule::create([
                                    'psychologist_id' => $request->psychologist_id,
                                    'day' => $schedule['day'],
                                    'time_start' => $schedule['time_start'],
                                    'time_end' => $schedule['time_end'],
                                ]);
            }
            return $this->respond(null);
        } else {
            return $this->errorNotFound('Psychologist id not found');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'psychologist_id' => 'required',
            'day' => 'required',
            'time_start' => 'required',
            'time_end' => 'required'
        ]);
        if (ChatSchedule::find($id) != null) {
            $chatschedule = ChatSchedule::findOrFail($id);
            $chatschedule->fill($request->all());
            $chatschedule->save();
            return $this->respond($chatschedule);
        } else {
            return $this->errorNotFound('invalid chat schedule id');
        }
    }

    public function delete($id)
    {
        $chatschedule = ChatSchedule::find($id);
        if(!$chatschedule) {
            return $this->errorNotFound('invalid chat schedule id');
        }

        $chatschedule->delete();
        return $this->respond($chatschedule);
    }

    public function get_detail_chat($id)
    {

        $schedule = ChatSchedule::where('psychologist_id',$id)->get();
        if(!$schedule) {
            return $this->errorNotFound('invalid psychologist id');
        } 
        $data['schedules'] = $schedule ;

        $relation = Relation::with('patient', 'patient.test')
                        ->where('psychologist_id',$id)->get();

        $data['chattings'] = $relation ;

        return $this->respond($data);
    }
}
