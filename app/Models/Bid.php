<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = [
        'request_id', 'craftsman_id', 'amount', 'duration_days',
        'message', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ═══════════ Relationships ═══════════
    public function request()   { return $this->belongsTo(Request::class); }
    public function craftsman() { return $this->belongsTo(User::class, 'craftsman_id'); }

    // ═══════════ Scopes ═══════════
    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeAccepted($q)  { return $q->where('status', 'accepted'); }
    public function scopeRejected($q)  { return $q->where('status', 'rejected'); }

    // ═══════════ Helpers ═══════════
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isAccepted(): bool  { return $this->status === 'accepted'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }

    // ═══════════ Accessors ═══════════
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'قيد الانتظار',
            'accepted'  => 'مقبول',
            'rejected'  => 'مرفوض',
            'withdrawn' => 'مسحوب',
            default     => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'   => '<span class="badge badge-pending">قيد الانتظار</span>',
            'accepted'  => '<span class="badge badge-approved">مقبول</span>',
            'rejected'  => '<span class="badge badge-cancelled">مرفوض</span>',
            'withdrawn' => '<span class="badge badge-cancelled">مسحوب</span>',
            default     => '',
        };
    }

    public function getDurationTextAttribute(): string
    {
        if ($this->duration_days === 1) return 'يوم واحد';
        if ($this->duration_days === 2) return 'يومان';
        if ($this->duration_days <= 10) return $this->duration_days . ' أيام';
        return $this->duration_days . ' يوماً';
    }
}
