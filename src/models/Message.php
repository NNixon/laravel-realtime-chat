<?php 

namespace NNixon\LaravelRealtimeChat\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'chat_messages';
    
    protected $fillable = [
        'user_id',
        'receiver_id',
        'message',
        'read'
    ];

    public function sender()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'receiver_id');
    }
}