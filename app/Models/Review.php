<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'request_id', 'client_id', 'craftsman_id', 'rating',
        'quality_rating', 'punctuality_rating', 'behavior_rating', 'comment',
    ];

    public function request()   { return $this->belongsTo(Request::class); }
    public function client()    { return $this->belongsTo(User::class, 'client_id'); }
    public function craftsman() { return $this->belongsTo(User::class, 'craftsman_id'); }
}
