<?php

namespace App\Http\Controllers\Api;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\Psychologist;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use stdClass;

class PsychologistController extends BaseController
{
    public function index()
    {
        $psychologists = Psychologist::all();
        return $this->respond($psychologists);
    }

    public function show($id)
    {
        $psychologist = Psychologist::find($id);
        if(!$psychologist) {
            return $this->errorNotFound('invalid psychologist id');
        }
        return $this->respond($psychologist);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'full_name' => 'required',
            'speciality' => 'required',
            'datebirth' => 'required',
            'graduation_university' => 'required',
            'graduation_year' => 'required',
            'city' => 'required',
            'province' => 'required',
            'str_number' => 'required',
        ]);
        if(Psychologist::where('user_id', $request->user_id)->first() == null){
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

                $psychologist = Psychologist::create($request->all());
                return $this->respond($psychologist);
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
            'full_name' => 'required',
            'speciality' => 'required',
            'datebirth' => 'required',
            'graduation_university' => 'required',
            'graduation_year' => 'required',
            'city' => 'required',
            'province' => 'required',
            'str_number' => 'required',
        ]);
        if (Psychologist::find($id) != null) {
            $psychologist = Psychologist::findOrFail($id);

            if($request->hasFile('avatar')){
                if($psychologist->image){
                    Storage::delete($psychologist->image);
                }
                $image = $request->file('avatar');
                $extension = $request->file('avatar')->extension();
                $imageName = $request->user_id;
                $imageName = time().'_'.$imageName.'.'.$extension;
                $imagePath = 'avatars/'.$imageName;
                Storage::putFileAs('', $image, $imagePath);
                $request['image'] = $imagePath;
            }

            $psychologist->fill($request->all());
            $psychologist->save();
            return $this->respond($psychologist);
        } else {
            return $this->errorNotFound('invalid psychologist id');
        }
    }

    public function delete($id)
    {
        $psychologist = Psychologist::find($id);
        if(!$psychologist) {
            return $this->errorNotFound('invalid psychologist id');
        }

        $psychologist->delete();
        return $this->respond($psychologist);
    }

    public function getMainDashboard(Request $request, $psychologist_id){
        $psychologist = Psychologist::find($psychologist_id);
        if(!$psychologist){
            return $this->errorNotFound('invalid psychologist id');
        }

        $data = new stdClass();

        $related_patients = Patient::with([
                                            'relations' => function ($query) use ($psychologist) {
                                                $query->where([['psychologist_id', $psychologist->id], ['is_active', true]]);
                                            },
                                            'relations.consults' => function ($query) use ($psychologist) {
                                                $query->orderBy('created_at', 'desc')->first();
                                            },
                                            'user'
                                            ])
                                            ->whereRelation('relations', [['psychologist_id', $psychologist->id], ['is_active', true]]);

        if($request->has('search_related')) {
            $search = $request->get('search_related');
            $related_patients = $related_patients->where('first_name', 'ILIKE', '%'.$search.'%')->orWhere('last_name', 'ILIKE', '%'.$search.'%');
        }
        $related_patients = $related_patients->get()->toArray();

        foreach($related_patients as $key=>$patient){
            $related_patients[$key]['status'] = [];
            $isVidCall = false;
            if(sizeof($patient['relations'][0]['consults']) > 0){
                if($patient['relations'][0]['consults'][0]['is_finished'] == true){
                    $isVidCall = false;
                }
                else{
                    $isVidCall = true;
                }
            }
            if($patient['latest_test'] == null && $isVidCall == false){
                array_push($related_patients[$key]['status'], 'konsultasi chat');
            }
            if($patient['latest_test']){
                if($patient['latest_test']['videocall_date'] == null){
                    array_push($related_patients[$key]['status'], 'input jadwal verifikasi');
                }
                else{
                    array_push($related_patients[$key]['status'], 'verifikasi tes');
                }
            }
            if($isVidCall == true){
                if($patient['relations'][0]['consults'][0]['videocall_link'] == null){
                    array_push($related_patients[$key]['status'], 'input link konsultasi');
                }
                else{
                    array_push($related_patients[$key]['status'], 'konsultasi video call');
                }
            }
        }
        $data->related_patients = $related_patients;

        $available_patients = Patient::where('is_dummy', $psychologist->is_dummy)
                                        ->doesntHave('relations')
                                        ->orWhereRelation('relations', 'is_active', false);

        if($request->has('search_available')) {
            $search = $request->get('search_available');
            $available_patients = $available_patients->where('first_name', 'ILIKE', '%'.$search.'%')->orWhere('last_name', 'ILIKE', '%'.$search.'%');
        }

        $available_patients = $available_patients->get()->where('has_relation', false)->where('latest_test', '<>', '');
        $data->available_patients = array_values($available_patients->toArray());

        return $this->respond($data);
    }

    public function getPatientList(Request $request, $psychologist_id){
        $psychologist = Psychologist::find($psychologist_id);
        if(!$psychologist){
            return $this->errorNotFound('invalid psychologist id');
        }

        $data = new stdClass();

        $related_patients = Patient::with(['relations' => function ($query) use ($psychologist) {
                                                    $query->where([['psychologist_id', $psychologist->id], ['is_active', true]]);
                                                }, 'user'],)
                                                ->whereRelation('relations', [['psychologist_id', $psychologist->id], ['is_active', true]]);

        if($request->has('search')) {
            $search = $request->get('search');
            $related_patients = $related_patients->where('first_name', 'ILIKE', '%'.$search.'%')->orWhere('last_name', 'ILIKE', '%'.$search.'%');
        }

        $data->related_patients = $related_patients->get();

        return $this->respond($data);
    }

}
