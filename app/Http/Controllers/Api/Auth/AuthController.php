<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Models\Patient;
use App\Models\Psychologist;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use DB;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{

	/*
	 * Register new user
	*/
	public function signup(Request $request) {
		
		$validation = Validator::make($request->all(), [
			'email' => 'required',
			'username' => 'required',
			'password' => 'required',
			'password_confirmation' => 'required',
			'role' => 'required'
		]);
		if($validation->fails()) {
            return $this->validationError();
        }

		$check = User::where('email', $request->email)->first();
		if($check){
			return $this->errorForbidden('Email yang digunakan sudah terdaftar!');
		}

		$check = User::where('username', $request->username)->first();
		if($check){
			return $this->errorForbidden('Username yang digunakan sudah terdaftar!');
		}

		if($request->password != $request->password_confirmation){
			return $this->errorForbidden('Masukkan password kembali dengan benar!');
		}

		$data = $request->all();
		$data['password'] = Hash::make($data['password']);

		$created = User::create($data);
		if($created) {
			$created->sendEmailVerificationNotification();

			$user = User::where('email', $created->email)->first();

			//admin, patient, psychologist
			$user->assignRole($request->role);
			
			// original
			return $this->respond(null);

			// login
			// $user['access_token'] = $user->createToken($request->email)->plainTextToken;
			// return $this->respond($user);
		}

		return $this->respondNotFound(null);
	}

	/*
	 * Generate sanctum token on successful login
	*/
	public function login(Request $request) {
		$validation = Validator::make($request->all(), [
			'email' => 'required',
			'password' => 'required',
		]);
		if($validation->fails()) {
            return $this->validationError();
        }

		$user = User::where('email', $request->email)->first();
		if(! $user){
			$user = User::where('username', $request->email)->first();
		}

		if (! $user || ! Hash::check($request->password, $user->password)) {
			return $this->errorNotFound('Email / username atau password salah!');
		}

		$user['access_token'] = $user->createToken($request->email)->plainTextToken;
		return $this->respond($user->makeHidden('roles'));
	}


	/*
	 * Revoke token; only remove token that is used to perform logout (i.e. will not revoke all tokens)
	*/
	public function logout(Request $request) {

		// Revoke the token that was used to authenticate the current request
		$request->user()->currentAccessToken()->delete();
		//$request->user->tokens()->delete(); // use this to revoke all tokens (logout from all devices)
		return $this->respond(null);
	}


	/*
	 * Get authenticated user details
	*/
	public function getAuthenticatedUser(Request $request) {
		$user = $request->user()->makeHidden('roles');
        return $this->respond($user);
	}


	public function sendPasswordResetLinkEmail(Request $request) {
		$validation = Validator::make($request->all(), [
			'email' => 'required',
		]);
		if($validation->fails()) {
            return $this->validationError();
        }

		$user = User::where('email', $request->email)->first();
		if(!$user){
			return $this->errorForbidden('Email yang dimasukkan belum terdaftar!');
		}

		$tokenData = DB::table('password_resets')->where('email', $request->email)->first();
		if($tokenData){
			DB::table('password_resets')->where('email', $request->email)->delete();
		}

		$status = Password::sendResetLink(
			$request->only('email')
		);

		if($status === Password::RESET_LINK_SENT) {
			return response()->json(['message' => __($status)], 200);
		} else {
			throw ValidationException::withMessages([
				'email' => __($status)
			]);
		}
	}

	public function validateTokenPasswordReset($token, Request $request) {
		$validation = Validator::make($request->all(), [
			'email' => 'required',
		]);
		if($validation->fails()) {
            return $this->validationError();
        }

		$user = User::where('email', $request->email)->first();
		if(!$user){
			return $this->errorForbidden('Email belum terdaftar!');
		}

		$tokenData = DB::table('password_resets')->where('email', $request->email)->first();
		if(!$tokenData){
			return $this->errorForbidden('Email tidak valid!');
		}
		if (! Hash::check($token, $tokenData->token)) {
			return $this->errorForbidden('Token tidak valid!');
		}
		$now = \Carbon\Carbon::now()->toDateTimeString();
		if(\Carbon\Carbon::parse($tokenData->created_at)->addMinutes(60) < $now){
			return $this->errorForbidden('Token expired!');
		}

		return $this->respond($tokenData);
	}

	public function resetPassword(Request $request) {
		$validation = Validator::make($request->all(), [
			'token' => 'required',
			'email' => 'required',
			'password' => 'required',
			'password_confirmation' => 'required',
		]);
		if($validation->fails()) {
            return $this->validationError();
        }

		$status = Password::reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),
			function ($user, $password) use ($request) {
				$user->forceFill([
					'password' => Hash::make($password)
				])->setRememberToken(Str::random(60));

				$user->save();

				event(new PasswordReset($user));
			}
		);

		if($status == Password::PASSWORD_RESET) {
			// If the user shouldn't reuse the token later, delete the token 
			// DB::table('password_resets')->where('email', $request->email)->delete();

			return response()->json(['message' => __($status)], 200);
		} else {
			throw ValidationException::withMessages([
				'email' => __($status)
			]);
		}
	}
}