<?php

namespace App\Http\Controllers;

use App\Events\MessagingBroadcast;
use App\Events\NotificationBroadcast;
use App\Models\ChatNotification;
use App\Models\ChatRooms;
use App\Models\ChatroomUsers;
use App\Models\Messaging;
use Hamcrest\Type\IsBoolean;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Pusher\Pusher;

class MessagingController extends Controller
{
    public function createChatRoom(Request $request, $booking_id)
    {
        $validate = $request->validate([
            'customer_id' => 'required|exists:users,id',
        ]);
        $chat_room = ChatRooms::updateOrCreate(
            ['booking_id'   => $booking_id,
             'appointer_id' => Auth::id(),
             'customer_id'  => $validate['customer_id']], 
            ['booking_id'   => $booking_id,
             'appointer_id' => Auth::id(),
             'customer_id'  => $validate['customer_id']]);
        return response(['data' => $chat_room->id]);
    }
    public function getChatRooms(Request $request)
    {
        $user = Auth::user()->load('userRole');
        $data = ChatRooms::when($user->userRole->role == 'customer', function($q) use($user){
                                $q->where('customer_id', $user->id)
                                  ->with('appointer.userBusiness');
                            }, function($q) use($user) {
                                $q->where('appointer_id', $user->id)
                                  ->with('customer');
                            })
                            ->has('chats')
                            ->withCount(['chatNotifications' => function($q){
                                $q->where('receiver_id', Auth::id());
                            }])
                            ->with('booking')
                            ->skip($request->skip)
                            ->take($request->take)
                            ->orderBy('id', 'desc')
                            ->get();
        return response([
            'data' => $data
        ]);
    }
    public function newMessage(Request $request, Messaging $messaging)
    {
        $validate = $request->validate([
            'message' => 'required',
            'room_id' => 'required|exists:chat_rooms,id',
            'reciever_id' => 'required|exists:users,id'
        ]);
        $message = $messaging->create([
            'message' => $validate['message'],
            'user_id' => Auth::id(),
            'chat_room_id' => $validate['room_id'], 
        ]);
        ChatNotification::create([
            'receiver_id' => $validate['reciever_id'],
            'chat_room_id' => $validate['room_id']
        ]);
        try {
            broadcast(new MessagingBroadcast($message->message, $message->chat_room_id))->toOthers();
            broadcast(new NotificationBroadcast($validate['reciever_id'], $message->chat_room_id, Auth::id()))->toOthers();
            return response(['data' => $message]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function getMessage(Request $request)
    {
        $validate = $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id'
        ]);
        $data = Messaging::where('chat_room_id', $validate['chat_room_id'])
                           ->orderBy('id', 'desc')
                           ->skip($request->skip)
                           ->take($request->take)
                           ->get();
        return response(['data' => $data], 200);
    }
    public function deleteNotification(Request $request)
    {
        $validate = $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'receiver_id' => 'required|exists:users,id'
        ]);

        $delete = ChatNotification::where('chat_room_id', $validate['chat_room_id'])
                          ->where('receiver_id', $validate['receiver_id'])
                          ->delete();
        return $delete ? true : false;
    }
}
