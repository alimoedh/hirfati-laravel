<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = [
        'request_id', 'total_amount', 'paid_amount', 'remaining_amount',
        'installment_count', 'current_installment', 'due_date', 'status',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
    ];

    public function request() { return $this->belongsTo(Request::class); }

    public function getPerInstallmentAmountAttribute(): float
    {
        return $this->installment_count > 0
            ? round($this->total_amount / $this->installment_count, 2)
            : 0;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }
}
