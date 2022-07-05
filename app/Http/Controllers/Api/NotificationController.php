<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function index()
    {
        $notifications = Notification::with(['user'])->orderByDesc('created_at')->get();
        return $this->respond($notifications);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'type' => 'required',
            'header' => 'required',
            'body' => 'required'
        ]);

        $user = User::find($request->user_id);
        if($user){
            $current_notification = Notification::where('user_id', $request->user_id)
                                                ->where('header', $request->header)
                                                ->first();
            if($current_notification){
                $current_notification->delete();
            }

            $notification = Notification::create($request->all());
            return $this->respond($notification);
        }
        else{
            return $this->errorNotFound('invalid user id');
        }
    }

    public function show($user_id)
    {
        $notification = Notification::with('user')->where('user_id', $user_id)->orderByDesc('created_at')->get();
        if(!$notification) {
            return $this->errorNotFound('invalid user_id');
        }
        return $this->respond($notification);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function delete($id)
    {
        $notification = Notification::find($id);
        if(!$notification) {
            return $this->errorNotFound('invalid notification id');
        }

        $notification->delete();
        return $this->respond($notification);
    }
}