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
        $chatschedule = ChatSchedule::find($id);
        if(!$chatschedule) {
            return $this->errorNotFound('invalid consult id');
        }
        return $this->respond($chatschedule);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'psychologist_id' => 'required',
            'day' => 'required',
            'time_start' => 'required',
            'time_end' => 'required'
        ]);
        if (Psychologist::find($request->psychologist_id) != null) {
            if(ChatSchedule::where('psychologist_id', $request->psychologist_id)->where('day', $request->day)->exists()){
                return $this->errorNotFound('The day has been used');
            }
            else{
                $chat_schedule = ChatSchedule::create($request->all());
                return $this->respond($chat_schedule);
            }
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
            return $this->respondNotFound('invalid chat schedule id');
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
        $data['schedule'] = $schedule ;

        $relation = Relation::with('patient', 'patient.test')
                        ->where('psychologist_id',$id)->where('status_chat', true)->get();

        $data['chatting'] = $relation ;

        return $this->respond($data);
    }
}
