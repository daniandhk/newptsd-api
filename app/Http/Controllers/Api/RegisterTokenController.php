<?php

namespace App\Http\Controllers\Api;

use App\Models\RegisterToken;
use Illuminate\Http\Request;

class RegisterTokenController extends BaseController
{
    public function index()
    {
        $token = RegisterToken::all();
        return $this->respond($token);
    }

    public function create()
    {
        RegisterToken::query()->delete();
        $date = \Carbon\Carbon::now()->addHours(48)->toDateTimeString();
        $token = RegisterToken::create(
            ['token' => strval(uniqid()), 'expired_at' => $date]
        );
        return $this->respond($token);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($token)
    {
        $data = RegisterToken::where('token', $token)->first();
        if($data){
            $date = \Carbon\Carbon::now();
            if($date > $data->expired_at){
                $data['is_valid'] = false;
            }
            else{
                $data['is_valid'] = true;
            }
        }
        else{
            $data['is_valid'] = false;
        }
        return $this->respond($data);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
