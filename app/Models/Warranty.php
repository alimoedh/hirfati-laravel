<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    protected $fillable = ['request_id', 'start_date', 'end_date', 'status', 'claim_reason', 'claim_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'claim_date' => 'date',
    ];

    public function request() { return $this->belongsTo(Request::class); }
}
