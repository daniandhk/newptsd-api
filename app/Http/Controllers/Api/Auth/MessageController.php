<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\MessageSent;
use App\Events\PrivateMessageSent;
use App\Http\Controllers\Api\BaseController;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;

class MessageController extends BaseController
{
    public function privateMessages(Request $request, User $user) {
        $privateCommunication= Message::with(['user', 'receiver'])
        ->where(['user_id'=> $request->user()->id, 'receiver_id'=> $user->id])
        ->orWhere(['user_id' => $user->id, 'receiver_id' => $request->user()->id])
        ->orderBy('created_at')->get();

        return $this->respond($privateCommunication);
    }

    public function sendPrivateMessage(Request $request, User $user) {
        $input=$request->all();
        $input['receiver_id'] = $user->id;
        $message=$request->user()->messages()->create($input);

        broadcast(new PrivateMessageSent($message->load('user')))->toOthers();
        
        return $this->respond($message);

    }
}
