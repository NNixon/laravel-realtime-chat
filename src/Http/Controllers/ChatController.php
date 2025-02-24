<?php 

namespace NNixon\LaravelRealtimeChat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use NNixon\LaravelRealtimeChat\Models\Message;
use NNixon\LaravelRealtimeChat\Events\MessageSent;

class ChatController extends Controller
{
    public function index()
    {
        $userModel = config('chat.user_model');
        $displayName = config('chat.user_display_name');
        
        $users = $userModel::where('id', '!=', auth()->id())
            ->select('id', $displayName)
            ->get();
            
        return view('laravel-chat::index', compact('users'));
    }

    public function getMessages($userId)
    {
        $messages = Message::where(function($query) use ($userId) {
            $query->where('user_id', auth()->id())
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('receiver_id', auth()->id());
        })
        ->with('sender')
        ->latest()
        ->paginate(config('chat.messages_per_page'));

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:' . config('chat.max_message_length')
        ]);

        $message = Message::create([
            'user_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        if (config('chat.broadcast_driver') === 'pusher') {
            broadcast(new MessageSent($message))->toOthers();
        }

        return response()->json($message);
    }
}