<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'request_id', 'sender_id', 'receiver_id', 'message',
        'is_read', 'is_voice', 'voice_url',
    ];

    protected $casts = [
        'is_read'  => 'boolean',
        'is_voice' => 'boolean',
    ];

    public function sender()   { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver() { return $this->belongsTo(User::class, 'receiver_id'); }
    public function request()  { return $this->belongsTo(Request::class); }

    public function getVoiceUrlAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }
}
