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
        $test_taken = Test::orderBy('created_at', 'desc');

        if($request->patient_id) {
            $patient = Patient::find($request->patient_id);
            if ($patient) {
                $test_taken = $test_taken->where('patient_id', $patient->id);
            } 
            else {
                return $this->errorNotFound('Patient id not found');
            }
        }

        if($request->test_type) {
            $test_type = TestType::where('type', 'ilike', $request->test_type)->first();
            if ($test_type) {
                $test_taken = $test_taken->where('test_type_id', $test_type->id);
            } 
            else {
                return $this->errorNotFound('test_type not found');
            }
        }

        $test_taken = $test_taken->get();

        return $this->respond($test_taken);
    }

    public function createTest(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'type'  => 'required',
            'name' => 'required',
            'delay_days' => 'required',
            'description' => 'required',
            'test_pages' => 'required',
            'submitter_id' => 'required',
        ]);
        if($validation->fails()) {
            return $this->validationError();
        }
        $test_type = TestType::where('type', 'ilike', $request->type)->first();
        if($test_type){
            return $this->errorForbidden('Kode tes sudah terpakai!');
        }

        $test_type = TestType::create([
            'type'  => strtolower($request->type),
            'name' => $request->name,
            'delay_days' => $request->delay_days,
            'description' => $request->description,
            'total_page' => count($request->test_pages),
            'submitter_id' => $request->submitter_id
         ]);

         $total_score = 0;
         foreach($request->test_pages as $key1=>$data) {
            $test_page = TestPage::create([
                'test_type_id' => $test_type['id'],
                'number' => $data['number'],
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            foreach($data['test_questions'] as $key2=>$q) {
                $qstion = TestQuestion::create([
                    'test_page_id' => $test_page['id'],
                    'text' => $q['text'],
                    'answer_type' => $q['answer_type'],
                ]);
    
                $max_score = 0;
                foreach($q['test_answers'] as $key3=>$ans) {
                    $answer = TestAnswer::create([
                        'test_question_id' => $qstion['id'],
                        'text' => $ans['is_essay'] ? "" : $ans['text'],
                        'description' => $ans['is_essay'] ? ($ans['description'] ? $ans['description'] : "Lainnya, harap dijelaskan") : null,
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

         return $this->respond($test_type);
    }

    public function updateTest(Request $request, $test_id)
    {
        $validation = Validator::make($request->all(), [
            'type'  => 'required',
            'name' => 'required',
            'delay_days' => 'required',
            'description' => 'required',
            'test_pages' => 'required',
            'updater_id' => 'required',
        ]);
        if($validation->fails()) {
            return $this->validationError();
        }
        $test_type = TestType::find($test_id);
        if(strtolower($request->type) != strtolower($test_type->type)){
            $find_type = TestType::where('type', 'ilike', $request->type)->first();
            if($find_type){
                return $this->errorForbidden('Kode tes sudah terpakai!');
            }
        }

        $test_type->fill([
            'type'  => strtolower($request->type),
            'name' => $request->name,
            'delay_days' => $request->delay_days,
            'description' => $request->description,
            'total_page' => count($request->test_pages),
            'updater_id' => $request->updater_id,
         ]);
         $test_type->save();

         $total_score = 0;
         foreach($request->test_pages as $key1=>$data) {
            $test_page = TestPage::find($data['id']);
            $test_page->fill([
                'test_type_id' => $test_type['id'],
                'number' => $data['number'],
                'title' => $data['title'],
                'description' => $data['description'],
            ]);
            $test_page->save();

            foreach($data['test_questions'] as $key2=>$q) {
                $qstion = TestQuestion::find($q['id']);
                $qstion->fill([
                    'test_page_id' => $test_page['id'],
                    'text' => $q['text'],
                    'answer_type' => $q['answer_type'],
                ]);
                $qstion->save();
    
                $max_score = 0;
                foreach($q['test_answers'] as $key3=>$ans) {
                    $answer = TestAnswer::find($ans['id']);
                    $answer->fill([
                        'test_question_id' => $qstion['id'],
                        'text' => $ans['is_essay'] ? "" : $ans['text'],
                        'description' => $ans['is_essay'] ? ($ans['description'] ? $ans['description'] : "Lainnya, harap dijelaskan") : null,
                        'weight' => $ans['weight'],
                        'is_essay' => $ans['is_essay'],
                    ]);
                    $answer->save();
    
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

         return $this->respond($test_type);
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

        $count = count(Test::where([['test_type_id', $request->test_type_id],['patient_id', $request->patient_id]])->get());

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
        }
        
        return $this->respond($test);
    }
}
