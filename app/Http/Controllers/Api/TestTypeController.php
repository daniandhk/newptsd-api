<?php

namespace App\Http\Controllers\Api;

use App\Models\TestType;
use Illuminate\Http\Request;

class TestTypeController extends BaseController
{
    public function index(Request $request)
    {
        $types = TestType::all();
        if($request->has('test_type')) {
            $test_type = $request->get('test_type');
            $types = $types->where('type', $test_type);
        }
        return $this->respond($types);
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
            'total_score' => 'required',
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
            'total_score' => 'required',
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
            return $this->respondNotFound('invalid TestType id');
        }

        $TestType->delete();
        return $this->respond($TestType);
    }
}
