<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'craftsman_id', 'request_id', 'type', 'amount',
        'balance_after', 'description', 'status',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function craftsman() { return $this->belongsTo(User::class, 'craftsman_id'); }
    public function request()   { return $this->belongsTo(Request::class); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'escrow_in'      => '💰 دفع معلق',
            'escrow_release' => '✅ تحرير دفعة',
            'commission'     => '🏢 عمولة',
            'withdraw'       => '🏦 سحب',
            'refund'         => '↩️ استرداد',
            default          => $this->type,
        };
    }
}
