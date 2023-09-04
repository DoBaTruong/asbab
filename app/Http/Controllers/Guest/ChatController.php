<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\Guest\ChatClient;
use App\Models\Message;
use Log;
use DB;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if($user) {
            $login = 1;
            $chats = Message::where('user_id', $user->id)->orWhere('repfor', $user->id)->orderBy('created_at', 'asc')->get();
        } else {
            $login = 0;
            $chats = [];
        }
        return response()->json([
            'chats' => $chats,
            'login' => $login,
            'logo' => 'guest/images/logo/logo.png',
            'default' => 'images/avatar/default.jpg'
        ]);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'message' => 'required'
        ]);
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if ($user->avatar === null) {
                $user->avatar = 'images/avatar/default.jpg';
            }
            $message = $user->messages()->create([
                'message' => $request->message,
                'type' => 'client',
                'repfor' => 0
            ]);

            broadcast(new ChatClient($user, $message));

            DB::commit();
            return response()->json([
                'message' => $message,
                'user' => $user,
                'baseUrl' => route('asbab.home')
            ], 200);                
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }
}
