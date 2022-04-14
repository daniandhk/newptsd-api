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

class AuthController extends BaseController
{

	/*
	 * Register new user
	*/
	public function signup(Request $request) {
		$validatedData = $request->validate([
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:6|confirmed',
			'role' => 'required'
		]);

		$validatedData['password'] = Hash::make($validatedData['password']);

		$created = User::create($validatedData);
		if($created) {
			$created->sendEmailVerificationNotification();

			$user = User::where('email', $created->email)->first();

			//admin, patient, psychologist
			$user->assignRole($request->role);
			
			// original
			// return response()->json(null, 201);

			// login
			$user['access_token'] = $user->createToken($request->email)->plainTextToken;
			return $this->respond($user);
		}

		return $this->respondNotFound(null);
	}

	/*
	 * Generate sanctum token on successful login
	*/
	public function login(Request $request) {
		$request->validate([
			'email' => 'required|email',
			'password' => 'required',
		]);

		$user = User::where('email', $request->email)->first();

		if (! $user || ! Hash::check($request->password, $user->password)) {
			return $this->errorNotFound('Email atau password salah!');
		}

		$user['access_token'] = $user->createToken($request->email)->plainTextToken;
		return $this->respond($user);
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
		$user = $request->user();
		$user->profile;
		$user->profile->guardian;
        return $this->respond($user);
	}


	public function sendPasswordResetLinkEmail(Request $request) {
		$request->validate(['email' => 'required|email']);

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

	public function resetPassword(Request $request) {
		$request->validate([
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:8|confirmed',
		]);

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
			return response()->json(['message' => __($status)], 200);
		} else {
			throw ValidationException::withMessages([
				'email' => __($status)
			]);
		}
	}
}