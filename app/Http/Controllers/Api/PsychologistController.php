<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
            return $this->respondNotFound('invalid psychologist id');
        }

        $psychologist->delete();
        return $this->respond($psychologist);
    }

}
