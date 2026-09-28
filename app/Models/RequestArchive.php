<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestArchive extends Model
{
    protected $table = 'requests_archive';
    protected $guarded = [];

    protected $casts = [
        'preferred_date'    => 'date',
        'warranty_end_date' => 'date',
        'archived_at'       => 'datetime',
    ];
}
