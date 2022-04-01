<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VerificationController extends Controller
{
    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(["message" => "Invalid/Expired url provided."], 401);
        }
    
        $user = User::findOrFail($user_id);
    
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    
        return response()->json(null, 200);
    }
    
    public function resend(Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(["message" => "Email already verified."], 400);
        }
    
        $request->user()->sendEmailVerificationNotification();
    
        return response()->json(["message" => "Email verification link sent on your email id"], 200);
    }
}
