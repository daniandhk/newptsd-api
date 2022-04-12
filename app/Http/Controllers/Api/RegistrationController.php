<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /**
     * Register User
     *
     * @param RegistrationRequest $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(RegistrationRequest $request) {
        $user = User::create($request->getAttributes());

        User::find($user->id)->assignRole('patient');

        dd(User::find($user->id)->getRoleNames());

        if($user){
            $user->sendEmailVerificationNotification();
            // User::all()->last()->assignRole('patient');
            return $this->respondWithMessage('User successfully created');
        }

        return $this->respondWithMessage('error');
    }

    public function register_psychologist(RegistrationRequest $request) {
        $user = User::create($request->getAttributes())->sendEmailVerificationNotification();

        User::all()->last()->assignRole('psychologist');

        return $this->respondWithMessage('User successfully created');
    }
}
