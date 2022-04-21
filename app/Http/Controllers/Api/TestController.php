<?php

namespace App\Http\Controllers\Api;

use App\Models\Test;
use App\Models\Answer;
use App\Models\Patient;
use App\Models\TestType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TestController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(is_null($request->patient_id)) {
            $test_taken = Test::with('patient','answer')
                        ->orderBy('created_at', 'desc')
                        ->get();
        } else {
            $patient = Patient::find($request->patient_id);
            if(!$patient) {
                return $this->errorNotFound('invalid patient id');
            }

            $test_taken = Test::with('patient','answer')
                        ->where('patient_id', $patient->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        return $this->respond($test_taken);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'patient_id' => 'required',
            'data_input' => 'required',
            'test_type' => 'required',
        ]);
        if($validation->fails()) {
            return $this->validationError();
        }

        if(!Patient::find($request->patient_id)) {
            return $this->errorNotFound('invalid patient id');
        }

        $test_type = TestType::where('type', $request->test_type)->first();
        if(!$test_type){
            return $this->errorNotFound('invalid test type id');
        }

        $score = 0;
        foreach($request->data_input as $type) {
            if($type['type'] == 'score' || $type['type'] == 'diagnosa'){
                $score = $this->sumTestScore($type);
                break;
            }
        }

        $test = Test::create([
            'patient_id'  => $request->patient_id,
            'score' => $score,
            'test_type_id' => $test_type->id,
         ]);

        foreach($request->data_input as $type) {
            foreach($type['answers'] as $no) {
                Answer::create([
                    'test_id' => $test->id,
                    'answer_text' => $no['resp'],
                    'answer_type' => $type['type'],
                    'question_index' => $no['index'],
                ]);
            }
        }
    }

    public function sumTestScore($answers) {
        $total = 0;
        foreach($answers['answers'] as $key=>$ans) {
            $total += $ans['resp'];
        }

        return $total;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function show($test_id)
    {
        $test = Test::with('patient','answer')->find($test_id);
        if(!$test) {
            return $this->errorNotFound('invalid test id');
        }

        $result['test'] = $test;

        return $this->respond($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function edit(Test $test)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Test $test)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
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
}
