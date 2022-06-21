<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Events\PrivateMessageSent;
use App\Http\Controllers\Api\BaseController;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;

class MessageController extends BaseController
{
    // public function __construct() {
    //     $this->middleware('auth');
    // }
    
    public function fetchMessages() {
        return Message::with('user')->get();
    }
   
    public function privateMessages(Request $request, User $user) {
        $privateCommunication= Message::with('user')
        ->where(['user_id'=> $request->user()->id, 'receiver_id'=> $user->id])
        ->orWhere(function($query) use($user, $request){
            $query->where(['user_id' => $user->id, 'receiver_id' => $request->user()->id]);
        })
        ->get();

        return $privateCommunication;
    }

    public function sendMessage(Request $request) {
        $message= $request->user()->messages()->create(['message'=>$request->message]);

        broadcast(new MessageSent(auth()->user(),$message->load('user')))->toOthers();
        
        return response(['status'=>'Message sent successfully','message'=>$message]);

    }

    public function sendPrivateMessage(Request $request,User $user) {
        $input=$request->all();
        $input['receiver_id']=$user->id;
        $message=$request->user()->messages()->create($input);

        broadcast(new PrivateMessageSent($message->load('user')))->toOthers();
        
        return response(['status'=>'Message private sent successfully','message'=>$message]);

    }
}
