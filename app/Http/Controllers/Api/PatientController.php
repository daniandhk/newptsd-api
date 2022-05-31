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
            'phone' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if (Patient::find($id) != null) {
            $patient = Patient::findOrFail($id);

            if($patient->image){
                File::delete([
                    public_path($patient->image),
                    public_path($patient->thumbnail),
                ]);
            }
            $image = request()->file('image');
            $imageName = $image->getClientOriginalName();
            $imageName = time().'_'.$imageName;
            $thumbnail = $image->getClientOriginalName();
            $thumbnail= time().'_thumbnail'.$thumbnail;

            Image::make($image)
            ->fit(100, 100)
            ->save(public_path('/images/').$thumbnail);
            $image->move(public_path('/images'), $imageName);
            $request['image'] = 'images/'.$imageName;
            $request['thumbnail'] = 'images/'.$thumbnail;

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

    public function getTestDashboard($patient_id){
        $patient = Patient::find($patient_id);
        if(!$patient){
            return $this->errorNotFound('invalid user id');
        }

        $data = new stdClass();

        //tests
        $data->test_types = TestType::all();

        if(count($data->test_types) > 0){
            foreach($data->test_types as $test_type) {
                $test_type->current_test = $test_type->tests
                                                        ->where('patient_id','=', $patient->id)
                                                        ->sortByDesc('created_at')
                                                        ->first();
            }
        }

        //chat and consult
        if($patient->relation){
            $relation = $patient->relation;
            $data->psychologist = Psychologist::with('chatSchedule')->find($relation->psychologist_id);
        }

        return $this->respond($data);
    }

    public function getConsultDashboard(Request $request, $patient_id){
        $patient = Patient::find($patient_id);
        if(!$patient){
            return $this->errorNotFound('invalid user id');
        }

        $data = new stdClass();

        //chat and consult
        if($patient->relation){
            $relation = $patient->relation;
            $data->psychologist = Psychologist::with('chatSchedule')->find($relation->psychologist_id);
            $data->consult = Consult::with(['consult_info','note_question'])->where('relation_id', $relation->id)->orderBy('created_at', 'desc')->first();
        }
        else{
            $psychologists = Psychologist::query()->with('chatSchedule');
            $per_page = 3;
            $request->whenHas('per_page', function($size) use (&$per_page) {
                $per_page = $size;
            });

            if($request->has('search')) {
                $search = $request->get('search');
                $psychologists = $psychologists->where('full_name', 'ILIKE', '%'.$search.'%');
            }

            //Paginate
            // $psychologists = $this->paginator($psychologists,$per_page);

            //Paginate Collection or Array
            $psychologists = $psychologists->get()->sortByDesc(function($psychologist){
                return ($psychologist->online_schedule['is_online']);
            });
            $psychologists = $this->paginateArray($psychologists,$per_page);

            $data->psychologists = $psychologists;
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
        $data->note_question = [];

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
            }
        }

        return $this->respond($data);
    }
}
