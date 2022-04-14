<?php

namespace App\Http\Controllers\Api;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    public function index()
    {
        $guardians = Guardian::all();
        return $this->respond($guardians);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'patient_id' => 'required',
            'full_name' => 'required',
            'status' => 'required',
            'phone' => 'required',
        ]);
        if(Guardian::where('patient_id', $request->patient_id)->first() == null){
            $guardian = Guardian::create($request->all());
            return $this->respond($guardian);
        } else {
            return $this->errorNotFound('Guardian id is already in use');
        }
    }

    public function show($id)
    {
        $guardian = Guardian::find($id);
        if(!$guardian) {
            return $this->errorNotFound('invalid guardian id');
        }
        return $this->respond($guardian);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'patient_id' => 'required',
            'full_name' => 'required',
            'status' => 'required',
            'phone' => 'required',
        ]);
        if (Guardian::find($id) != null) {
            $guardian = Guardian::findOrFail($id);
            $guardian->fill($request->all());
            $guardian->save();
            return $this->respond($guardian);
        } else {
            return $this->errorNotFound('invalid guardian id');
        }
    }

    public function delete($id)
    {
        $guardian = Guardian::find($id);
        if(!$guardian) {
            return $this->respondNotFound('invalid guardian id');
        }

        $guardian->delete();
        return $this->respond($guardian);
    }
}
