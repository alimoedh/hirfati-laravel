<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'discount_percent', 'discount_amount', 'min_order',
        'max_uses', 'used_count', 'expiry_date', 'is_active',
    ];

    protected $casts = ['expiry_date' => 'date', 'is_active' => 'boolean'];
}
