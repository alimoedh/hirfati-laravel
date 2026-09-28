<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['request_id', 'client_id', 'craftsman_id', 'last_message_at', 'is_active'];
    protected $casts    = ['last_message_at' => 'datetime', 'is_active' => 'boolean'];

    public function request()   { return $this->belongsTo(Request::class); }
    public function client()    { return $this->belongsTo(User::class, 'client_id'); }
    public function craftsman() { return $this->belongsTo(User::class, 'craftsman_id'); }
}
