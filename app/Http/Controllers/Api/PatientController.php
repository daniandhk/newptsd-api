<?php

namespace App\Http\Controllers\Api;

use App\Models\Consult;
use App\Models\Journal;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Psychologist;
use App\Models\Relation;
use App\Models\Test;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;
use stdClass;

class PatientController extends BaseController
{
    public function index()
    {
        $patients = Patient::all();
        return $this->respond($patients);
    }

    public function show($id)
    {
        $patient = Patient::find($id);
        if(!$patient) {
            return $this->errorNotFound('invalid patient id');
        }
        return $this->respond($patient);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'datebirth' => 'required',
            'city' => 'required',
            'province' => 'required',
            'phone' => 'required'
        ]);
        if(Patient::where('user_id', $request->user_id)->first() == null){
            if (User::find($request->user_id)->email_verified_at != null) {
                $patient = Patient::create($request->all());
                return $this->respond($patient);
            } else {
                return $this->errorNotFound('Your email has not been verified');
            }
        } else {
            return $this->errorNotFound('User id is already in use');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'datebirth' => 'required',
            'city' => 'required',
            'province' => 'required',
            'phone' => 'required'
        ]);
        if (Patient::find($id) != null) {
            $patient = Patient::findOrFail($id);
            $patient->fill($request->all());
            $patient->save();
            return $this->respond($patient);
        } else {
            return $this->errorNotFound('invalid patient id');
        }
    }

    public function delete($id)
    {
        $patient = Patient::find($id);
        if(!$patient) {
            return $this->respondNotFound('invalid patient id');
        }

        $patient->delete();
        return $this->respond($patient);
    }

    public function getDashboard($user_id){
        $patient = Patient::where('user_id', $user_id)->first();
        if(!$patient){
            return $this->errorNotFound('invalid user id');
        }

        $data = new stdClass();

        //tests
        $tests = Test::where('patient_id', $patient->id)->orderBy('created_at', 'desc')->get();
        if(count($tests) == 0){
            $data->test = null;
        }
        else{
            $test = $tests->first();
            $data->test = $test;
        }

        //chat and consult
        if($patient->relation){
            $relation = $patient->relation;
            $data->psychologists[0] = Psychologist::with('chatSchedule')->find($relation->psychologist_id);
            $data->consult = Consult::with(['consult_info','note_question'])->where('relation_id', $relation->id)->orderBy('created_at', 'desc')->first();
        }
        else{
            $data->psychologists = Psychologist::with('chatSchedule')->get();
            $data->consult = null;
        }

        //jurnal
        $today = date("Y-m-d");
        $journals = Journal::where('user_id', $user_id)->get();
        if(count($journals)){
            foreach($journals as $j) {
                $date = $j->created_at->format("Y-m-d");
                if($today == $date){
                    $data->journal = $j;
                    break;
                }
                $data->journal = null;
            }
        }
        else{
            $data->journal = null;
        }

        return $this->respond($data);
    }

    public function getJournalByDate(Request $request, $user_id){
        $patient = Patient::where('user_id', $user_id)->first();
        if(!$patient){
            return $this->errorNotFound('invalid user id');
        }
        $date = $request->date;
        if(!$date){
            return $this->errorNotFound('require date');
        }
        else{
            $format = 'Y-m-d';
            $d = DateTime::createFromFormat($format, $date);
            if(!($d && $d->format($format) === $date)){
                return $this->errorNotFound('invalid date');
            }
        }

        $data = new stdClass();

        //jurnal
        $journals = Journal::where('user_id', $user_id)->get();
        if(count($journals)){
            foreach($journals as $j) {
                $journal_date = date("Y-m-d", strtotime($j->created_at));
                if($date == $journal_date){
                    $data->journal = $j;
                    break;
                }
                $data->journal = null;
            }
        }
        else{
            $data->journal = null;
        }

        //chat and consult
        if($patient->relation){
            $relation = $patient->relation;
            $consult = Consult::with(['consult_info','note_question','note_question.note_answer'])->where('relation_id', $relation->id)->orderBy('created_at', 'desc')->first();
        }
        else{
            $consult = null;
        }

        if($consult){
            if(count($consult->note_question) > 0){
                $next_date = $consult->next_date;
                $nextDate = date("Y-m-d", strtotime($next_date));
                $last_date = $consult->last_date;
                $lastDate = date("Y-m-d", strtotime($last_date));
                if($date <= $nextDate && $date >= $lastDate){
                    foreach($consult->note_question as $question) {
                        if(count($question->note_answer) > 0){
                            foreach($question->note_answer as $answer) {
                                $answer_date = $answer->date;
                                $newAnswerDate = date("Y-m-d", strtotime($answer_date));
                                if($date == $newAnswerDate){
                                    $question->answer = $answer->answer_text;
                                    break;
                                }
                                $question->answer = null;
                            }
                        }
                        else{
                            $question->answer = null;
                        }
                    }
                    $data->note_question = $consult->note_question;
                }
                else{
                    $data->note_question = [];
                }
            }
            else{
                $data->note_question = [];
            }
        }
        else{
            $data->note_question = [];
        }

        return $this->respond($data);
    }
}
