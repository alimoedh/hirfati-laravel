<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'ip_address', 'user_agent', 'login_time'];
    protected $casts    = ['login_time' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}
