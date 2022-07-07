<?php

namespace App\Http\Controllers\Api;

use App\Models\Consult;
use App\Models\Journal;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Psychologist;
use App\Models\TestType;
use App\Models\User;
use DateTime;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use stdClass;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
            'phone' => 'required',
        ]);
        if(Patient::where('user_id', $request->user_id)->first() == null){
            if (User::find($request->user_id)->email_verified_at != null) {
                if($request->hasFile('avatar')){
                    $image = $request->file('avatar');
                    $extension = $request->file('avatar')->extension();
                    $imageName = $request->user_id;
                    $imageName = time().'_'.$imageName.'.'.$extension;
                    $imagePath = 'avatars/'.$imageName;
                    Storage::putFileAs('', $image, $imagePath);
                    $request['image'] = $imagePath;
                }

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
            'phone' => 'required',
        ]);
        if (Patient::find($id) != null) {
            $patient = Patient::findOrFail($id);

            if($request->hasFile('avatar')){
                if($patient->image){
                    Storage::delete($patient->image);
                }
                $image = $request->file('avatar');
                $extension = $request->file('avatar')->extension();
                $imageName = $request->user_id;
                $imageName = time().'_'.$imageName.'.'.$extension;
                $imagePath = 'avatars/'.$imageName;
                Storage::putFileAs('', $image, $imagePath);
                $request['image'] = $imagePath;
            }

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
            return $this->errorNotFound('invalid patient id');
        }

        $patient->delete();
        return $this->respond($patient);
    }

    public function getTestDashboard($patient_id){
        $patient = Patient::find($patient_id);
        if(!$patient){
            return $this->errorNotFound('invalid user id');
        }

        $data = new stdClass();

        //tests
        $data->test_types = TestType::FilteredTest($patient_id)->get();

        //chat and consult
        $relation = $patient->relations->where('is_active', true)->first();
        if($relation){
            $data->psychologist = Psychologist::with('chat_schedules')->find($relation->psychologist_id);
        }

        return $this->respond($data);
    }

    public function getConsultDashboard(Request $request, $patient_id){
        $patient = Patient::find($patient_id);
        if(!$patient){
            return $this->errorNotFound('invalid patient id');
        }

        $data = new stdClass();

        //tests
        $data->test_types = TestType::FilteredTest($patient_id)->get();

        //chat and consult
        $relation = $patient->relations->where('is_active', true)->first();
        if($relation){
            $data->relation_id = $relation->id;
            $data->psychologist = Psychologist::with('chat_schedules')->find($relation->psychologist_id);
            $data->consult = Consult::with('note_questions')->where('relation_id', $relation->id)->orderBy('created_at', 'desc')->first();
        }
        else{
            $psychologists = Psychologist::where('is_dummy', $patient->is_dummy)->with('chat_schedules');

            if($request->has('search')) {
                $search = $request->get('search');
                $psychologists = $psychologists->where('full_name', 'ILIKE', '%'.$search.'%');
            }

            $psychologists = $psychologists->get()->sortByDesc(function($psychologist){
                return ($psychologist->online_schedule['is_online']);
            });

            $data->psychologists = array_values($psychologists->toArray());
            $data->consult = null;
        }

        return $this->respond($data);
    }

    public function getJournalByDate(Request $request, $patient_id){
        $patient = Patient::find($patient_id);
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
        $data->journal = null;

        $journals = Journal::where('patient_id', $patient_id)->get();
        if(count($journals)){
            foreach($journals as $j) {
                $journal_date = date("Y-m-d", strtotime($j->created_at));
                if($date == $journal_date){
                    $data->journal = $j;
                    break;
                }
            }
        }

        //note_question
        $data->note_questions = [];

        $relation = $patient->relations->where('is_active', true)->first();
        if($relation){
            $consults = Consult::with(['note_questions','note_questions.note_answers'])->where('relation_id', $relation->id)->orderBy('created_at', 'desc')->get();
            foreach($consults as $consult) {
                $next_date = $consult->videocall_date;
                $nextDate = date("Y-m-d", strtotime($next_date));
                $last_date = $consult->created_at;
                $lastDate = date("Y-m-d", strtotime($last_date));
                if($date <= $nextDate && $date >= $lastDate){
                    if(count($consult->note_questions) > 0){
                        foreach($consult->note_questions as $question) {
                            if(count($question->note_answers) > 0){
                                foreach($question->note_answers as $answer) {
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
                    }
                    $data->note_questions = $consult->note_questions;
                    $data->consult_id = $consult->id;
                    break;
                }
            }
        }
        else{
            $consult = null;
        }

        return $this->respond($data);
    }
}
