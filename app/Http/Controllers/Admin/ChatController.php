<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\Admin\ChatAdmin;
use Log;
use DB;
use Cache;

class ChatController extends Controller
{
    public function index(Request $request)
   {
       if (isset($request->active_id)) {
            $activeId = $request->active_id;
       } else {
            $activeId = 0;
            foreach (Message::orderBy('created_at', 'desc')->get() as $chat) {
                if ($chat->latest != null) {
                    if ($activeId == 0) {
                        $activeId = $chat->id;
                    } else {
                        if (Message::find($activeId)->latest > $chat->latest) {
                            $activeId = $chat->id;
                        }                   
                    }
                } else {
                    $activeId = Message::orderBy('created_at', 'desc')->first()->id;
                }
            }
        }

        $userChats = [];

        if ($activeId !== 0) {
            Message::find($activeId)->update([
                'latest' => date('Y-m-d H:i:s')
            ]);
        
            if(Message::find($activeId)->repfor == 0) {
                $userActive = Message::find($activeId)->user_id;
            } else {
                $userActive = Message::find($activeId)->repfor;
            }

            $activeChats = Message::where('user_id', $userActive)->orWhere('repfor', $userActive)->get();
            
            if($activeChats !== null) {
                foreach ($activeChats as $a) {
                    $a->update([
                        'read' => 1
                    ]);
                }
            }

            foreach (Message::orderBy('created_at', 'desc')->get() as $chat) {
                if($chat->repfor == 0) {
                    if (isset($userChats[$chat->user_id])) {
                        if($userChats[$chat->user_id]->created_at > $chat->created_at) {
                            $userChats[$chat->user_id] = $chat;
                        }
                    } else {
                        $userChats[$chat->user_id] = $chat;
                    }
                } else {
                    if (isset($userChats[$chat->repfor])) {
                        if($userChats[$chat->repfor]->created_at > $chat->created_at) {
                            $userChats[$chat->repfor] = $chat;
                        }
                    } else {
                        $userChats[$chat->repfor] = $chat;
                    }
                }
            }

            $user = User::find($userActive);
        } else {
            $activeChats = $user = $userChats = null;
        }

        return view('admin.chat.index', compact('user','activeId','userChats','activeChats'));
   }

   public function data($id, Request $request)
   {
        $user = User::find($id);
        Message::find($request->contact)->update([
            'latest' => date('Y-m-d H:i:s')
        ]);
        $chats = Message::where('user_id', $id)->orWhere('repfor', $id)->get();
        foreach ($chats as $chat) {
            $chat->update([
                'read' => 1
            ]);
        }
        return response()->json([
           'chats' => $chats,
           'user' => $user,
           'logo' => asset('guest/images/logo/logo.png') 
        ], 200);
   }

   public function store(Request $request)
   {
        $validator = $request->validate([
            'message' => 'required'
        ]);
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $user->avatar = 'guest/images/logo/logo.png';
            $message = Message::create([
                'user_id' => $user->id,
                'message' => $request->message,
                'type' => 'admin',
                'repfor' => $request->user_id
            ]);

            $avatar = $user->avatar;
            $chatContent = $message->message;

            broadcast(new ChatAdmin($avatar, $chatContent));

            DB::commit();

            return response()->json([
                'message' => $message,
                'user' => $user
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

   public function search(Request $request)
   {
       $users = User::where('name', 'LIKE', '%'.$request->keyword.'%')->get();
       if ($users) {
            $chats = [];
            foreach ($users as $u) {
                $chat = Message::where('user_id', $u->id)->orWhere('repfor', $u->id)->orderBy('created_at', 'desc')->first();
                if ($chat) {
                    if (Cache::has('user-is-online-' . $u->id)) {
                        $chat->user->online = 1;
                    } else {
                        $chat->user->online = 0;
                    }
                    $chats[] = $chat;
                }
            }
       } else {
           $chats = null;
       }
       return response()->json($chats, 200);
   }
}
