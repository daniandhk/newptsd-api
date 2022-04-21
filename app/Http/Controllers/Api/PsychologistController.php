<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Support\Facades\File;
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if (Psychologist::find($id) != null) {
            $psychologist = Psychologist::findOrFail($id);

            if($psychologist->image){
                File::delete([
                    public_path($psychologist->image),
                    public_path($psychologist->thumbnail),
                ]);
            }
            $image = request()->file('image');
            $imageName = $image->getClientOriginalName();
            $imageName = time().'_'.$imageName;
            $thumbnail = $image->getClientOriginalName();
            $thumbnail= time().'_thumbnail'.$thumbnail;

            Image::make($image)
            ->fit(100, 100)
            ->save(public_path('/images/').$thumbnail);
            $image->move(public_path('/images'), $imageName);
            $request['image'] = 'images/'.$imageName;
            $request['thumbnail'] = 'images/'.$thumbnail;

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
