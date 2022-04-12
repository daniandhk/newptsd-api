<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Relation;
use App\Models\Psychologist;
use App\Models\Patient;

class RelationController extends BaseController
{
    public function index(Request $request)
    {
        if(is_null($request->patient_id)) {
            $relations = Relation::all();
        } else {
            $patient = Patient::find($request->patient_id);
            if(!$patient) {
                return $this->errorNotFound('invalid patient id');
            }

            $relations = Relation::where('patient_id', $patient->id)->get();
        }
        
        return $this->respond($relations);
    }

    public function show($id)
    {
        $relation = Relation::findOrFail($id);
        return $this->respond($relation);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'patient_id' => 'required',
            'psychologist_id' => 'required',
            'patient_user_id' => 'required',
            'psychologist_user_id' => 'required',
            'status_test' => 'required',
            'status_chat' => 'required'
        ]);

        if (Relation::where('patient_id',$request->patient_id)->where('psychologist_id',$request->psychologist_id)->first() == null) {
            if (Patient::find($request->patient_id) != null) {
                if (Psychologist::find($request->psychologist_id) != null) {
                    Patient::find($request->patient_id)->update(['have_relation'=>true]);
                    $relation = Relation::create($request->all());
                    return $this->respond($relation);
                } else {
                    return $this->errorNotFound('Psychologist id not found');
                }
            } else {
                return $this->errorNotFound('Patient id not found');
            }
        } else {
            return $this->errorNotFound('Patient id or Psychologist id has been used');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'patient_id' => 'required',
            'psychologist_id' => 'required',
            'patient_user_id' => 'required',
            'psychologist_user_id' => 'required',
            'status_test' => 'required',
            'status_chat' => 'required'
        ]);
        if (Relation::find($id) != null) {
            if (Relation::where('patient_id',$request->patient_id)->where('psychologist_id',$request->psychologist_id)->first() == null) {    
                $relation = Relation::findOrFail($id);
                $relation->fill($request->all());
                $relation->save();
                return $this->respond($relation);
            } else {
                return $this->errorNotFound('id has been used');
            }
        } else {
            return $this->errorNotFound('invalid relation id');
        }
    }

    public function delete($id)
    {
        $relation = Relation::find($id);
        if(!$relation) {
            return $this->respondNotFound('invalid relation id');
        }

        $relation->delete();
        return $this->respond($relation);
    }

    public function get_patient($id)
    {
        $relation = Relation::with('patient')
                        ->where('psychologist_id',$id)->get();
        if(!$relation) {
            return $this->errorNotFound('invalid psychologist id');
        } 
        $data['registered'] = $relation;

        $available = Patient::leftjoin('relations', 'patients.id', '=', 'relations.patient_id')
        ->whereNull('relations.patient_id')->get();
 
        
        $data['available'] = $available;
        return $this->respond($data);
    }
}
