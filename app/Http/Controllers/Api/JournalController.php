<?php

namespace App\Http\Controllers\Api;

use App\Models\Journal;
use Illuminate\Http\Request;
use App\Models\Psychologist;
use App\Models\Patient;

class JournalController extends BaseController
{
    public function index(Request $request)
    {
        if(is_null($request->user_id)) {
            $journals = Journal::all();
        } else {
            $patient = Patient::where('user_id', $request->user_id)->first();
            if(!$patient) {
                return $this->errorNotFound('invalid User id');
            }

            $journals = Journal::where('user_id', $patient->user_id)->get();
        }
        
        return $this->respond($journals);
    }

    public function show($id)
    {
        $journal = Journal::findOrFail($id);
        return $this->respond($journal);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'text' => 'required',
        ]);

        if (Patient::where('user_id', $request->user_id)->first() != null) {
            $journal = Journal::create($request->all());
            return $this->respond($journal);
        } else {
            return $this->errorNotFound('User id not found');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'text' => 'required',
        ]);
        if (Journal::find($id) != null) {
            $journal = Journal::find($id);
            $journal->fill($request->all());
            $journal->save();
            return $this->respond($journal);
        } else {
            return $this->errorNotFound('invalid journal id');
        }
    }

    public function delete($id)
    {
        $journal = Journal::find($id);
        if(!$journal) {
            return $this->respondNotFound('invalid journal id');
        }

        $journal->delete();
        return $this->respond($journal);
    }
}
