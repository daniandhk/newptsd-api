<?php

namespace App\Http\Controllers\Api;

use App\Models\Patient;
use App\Models\TestType;
use Illuminate\Http\Request;

class TestTypeController extends BaseController
{
    public function index(Request $request)
    {
        $types = TestType::with(['submitter', 'updater']);
        if($request->has('test_type')) {
            $test_type = $request->get('test_type');
            $types = $types->where('type', $test_type)->first();
        }
        else{
            $types = $types->get();
        }
        return $this->respond($types);
    }

    public function getTestQuestions(Request $request)
    {
        if($request->has('test_type')) {
            $types = TestType::with(
                'test_pages', 
                'test_pages.test_questions', 
                'test_pages.test_questions.test_answers',
            );
            $test_type = $request->get('test_type');
            $type = $types->where('type', 'ilike', $test_type)->first();
            if($type){
                return $this->respond($type);
            }
            else{
                return $this->errorNotFound('invalid TestType');
            }
        }
        else{
            return $this->errorNotFound('invalid TestType');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'name' => 'required',
            'description' => 'required',
            'delay_days' => 'required',
            'submitter_id' => 'required',
        ]);
        $type = TestType::create($request->all());
        return $this->respond($type);
    }

    public function show($id)
    {
        $TestType = TestType::find($id);
        if(!$TestType) {
            return $this->errorNotFound('invalid TestType id');
        }
        return $this->respond($TestType);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'type' => 'required',
            'name' => 'required',
            'description' => 'required',
            'delay_days' => 'required',
            'submitter_id' => 'required',
        ]);
        if (TestType::find($id) != null) {
            $TestType = TestType::findOrFail($id);
            $TestType->fill($request->all());
            $TestType->save();
            return $this->respond($TestType);
        } else {
            return $this->errorNotFound('invalid TestType id');
        }
    }

    public function delete($id)
    {
        $TestType = TestType::find($id);
        if(!$TestType) {
            return $this->errorNotFound('invalid TestType id');
        }

        $TestType->delete();
        return $this->respond($TestType);
    }
}
