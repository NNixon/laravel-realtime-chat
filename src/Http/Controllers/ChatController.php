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
        $users = config('auth.providers.users.model')::where('id', '!=', auth()->id())->get();
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
        })->with('sender')->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'user_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }
}