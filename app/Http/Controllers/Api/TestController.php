<?php

namespace App\Http\Controllers\Api;

use App\Models\Test;
use App\Models\Answer;
use App\Models\NoteQuestion;
use App\Models\Patient;
use App\Models\TestAnswer;
use App\Models\TestPage;
use App\Models\TestQuestion;
use App\Models\TestType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TestController extends BaseController
{
    public function index(Request $request)
    {
        if(is_null($request->patient_id)) {
            $test_taken = Test::orderBy('created_at', 'desc')->get();
        } else {
            $patient = Patient::find($request->patient_id);
            if(!$patient) {
                return $this->errorNotFound('invalid patient id');
            }

            $test_taken = Test::where('patient_id', $patient->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        return $this->respond($test_taken);
    }

    public function createTest(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'test_type' => 'required',
            'data_input' => 'required',
        ]);
        if($validation->fails()) {
            return $this->validationError();
        }
        $test_type = TestType::where('type', $request->test_type->type)->first();
        if($test_type){
            return $this->errorForbidden('code test already used');
        }

        $test_type = TestType::create([
            'type'  => $request->test_type->type,
            'name' => $request->test_type->name,
            'delay_days' => $request->test_type->delay_days,
            'description' => $request->test_type->description,
            'total_page' => $request->test_type->total_page,
         ]);

         $total_score = 0;
         foreach($request->data_input as $key1=>$data) {
            $test_page = TestPage::create([
                'test_type_id' => $test_type['id'],
                'number' => $data['page_number'],
                'title' => $data['page_title'],
                'description' => $data['page_description'],
            ]);

            foreach($data['questions'] as $key2=>$q) {
                $qstion = TestQuestion::create([
                    'test_page_id' => $test_page['id'],
                    'text' => $q['text'],
                    'answer_type' => $q['answer_type'],
                ]);
    
                $max_score = 0;
                foreach($q['answers'] as $key3=>$ans) {
                    $answer = TestAnswer::create([
                        'test_question_id' => $qstion['id'],
                        'text' => $ans['text'],
                        'description' => $ans['description'],
                        'weight' => $ans['weight'],
                        'is_essay' => $ans['is_essay'],
                    ]);
    
                    if ($ans['weight'] > $max_score){
                        $max_score = $ans['weight'];
                    }
                }
    
                $total_score += $max_score;
            }

         }

         $test_type = TestType::find($test_type['id']);
         $test_type->total_score = $total_score;
         $test_type->save();

         return $this->respond(null);
    }

    public function storePatientAnswers(Request $request)
    {
        $this->validate($request, [
            'patient_id' => 'required',
            'test_type_id' => 'required',
            'pages' => 'required',
        ]);

        if(!Patient::find($request->patient_id)) {
            return $this->errorNotFound('invalid patient id');
        }

        if(!TestType::find($request->test_type_id)){
            return $this->errorNotFound('invalid test type id');
        }

        $count = count(Test::where('patient_id', $request->patient_id)->get());

        $test = Test::create([
            'patient_id'  => $request->patient_id,
            'test_type_id' => $request->test_type_id,
            'index' => $count+1,
         ]);

        $sum_score = 0;
        foreach($request->pages as $page) {
            foreach($page['questions'] as $question) {
                foreach($question['answers'] as $patient_answer) {
                    Answer::create([
                        'test_id' => $test['id'],
                        'test_answer_id' => $patient_answer['id'],
                        'test_question_id' => $patient_answer['test_question_id'],
                        'text' => $patient_answer['text'],
                    ]);
                
                    if($question['answer_type'] == 'score'){
                        $test_ans = TestAnswer::find($patient_answer['id']);
                        $sum_score += $test_ans['weight'];
                    }
                }
            }
        }

        $test = Test::find($test['id']);
        $test->score = $sum_score;
        $test->save();

        return $this->respond(null);
    }

    public function show($test_id)
    {
        $test = Test::with('patient','answers')->find($test_id);
        // if(!$test) {
        //     return $this->errorNotFound('invalid test id');
        // }

        return $this->respond($test);
    }

    public function edit(Test $test)
    {
        //
    }

    public function update(Request $request, Test $test)
    {
        //
    }

    public function destroy($test_id)
    {
        $test = Test::find($test_id);
        if(!$test) {
            return $this->errorNotFound('invalid test id');
        }

        $test->delete();
        return $this->respond($test);
    }

    public function updateVideoCall(Request $request, $test_id) {
        $test = Test::find($test_id);
        if(!$test) {
            return $this->errorNotFound('invalid test id');
        }

        $validation = Validator::make($request->all(), [
            'videocall_link' => 'required',
            'videocall_date' => 'required|date',
        ]);
        if($validation->fails()) {
            return $this->validationError();
        }

        $test->videocall_link = $request->videocall_link;
        $test->videocall_date = $request->videocall_date;

        $test->save();
        return $this->respond($test);
    }

    public function finishTest(Request $request, $test_id) {
        $test = Test::find($test_id);
        if(!$test) {
            return $this->errorNotFound('invalid test id');
        }

        $test->is_finished = true;
        $test->save();

        if($request->is_consult == true){
            $request->not_json = true;
            $consult = json_decode(app('App\Http\Controllers\Api\ConsultController')->store($request));

            if(is_array($request->notes) && sizeof($request->notes) > 0){
                foreach($request->notes as $note){
                    if($note['question_text']){
                        NoteQuestion::create([
                            'consult_id' => $consult->id,
                            'question_text' => $note['question_text'],
                        ]);
                    }
                }
            }
        }
        
        return $this->respond($test);
    }
}
