<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CraftsmanWallet extends Model
{
    protected $fillable = [
        'craftsman_id', 'balance', 'escrow_balance',
        'total_earned', 'total_commission', 'total_withdrawn',
    ];

    protected $casts = [
        'balance'          => 'decimal:2',
        'escrow_balance'   => 'decimal:2',
        'total_earned'     => 'decimal:2',
        'total_commission' => 'decimal:2',
        'total_withdrawn'  => 'decimal:2',
    ];

    public function craftsman() { return $this->belongsTo(User::class, 'craftsman_id'); }

    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(['craftsman_id' => $userId]);
    }
}
