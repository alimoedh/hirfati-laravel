<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'client_id', 'craftsman_id', 'category_id', 'title', 'description',
        'address', 'preferred_date', 'preferred_time', 'budget', 'ai_estimate',
        'status', 'is_emergency', 'use_installment', 'installment_count',
        'problem_image', 'has_warranty', 'warranty_end_date',
        'latitude', 'longitude',
    ];

    protected $casts = [
        'preferred_date'    => 'date',
        'is_emergency'      => 'boolean',
        'use_installment'   => 'boolean',
        'has_warranty'      => 'boolean',
        'warranty_end_date' => 'date',
        'budget'            => 'decimal:2',
        'ai_estimate'       => 'decimal:2',
    ];

    // ========== Relationships ==========
    public function client()       { return $this->belongsTo(User::class, 'client_id'); }
    public function craftsman()    { return $this->belongsTo(User::class, 'craftsman_id'); }
    public function category()     { return $this->belongsTo(Category::class); }
    public function messages()     { return $this->hasMany(Message::class); }
    public function installments() { return $this->hasMany(Installment::class); }
    public function review()       { return $this->hasOne(Review::class); }
    public function complaint()    { return $this->hasOne(Complaint::class); }
    public function warranty()     { return $this->hasOne(Warranty::class); }
        public function bids() { return $this->hasMany(Bid::class); }
            public function workPhotos() { return $this->hasMany(WorkPhoto::class); }
    public function beforePhotos() { return $this->hasMany(WorkPhoto::class)->where('type', 'before'); }
    public function afterPhotos()  { return $this->hasMany(WorkPhoto::class)->where('type', 'after'); }


    // ========== Scopes ==========
    public function scopePending($q)    { return $q->where('status', 'pending'); }
    public function scopeCompleted($q)  { return $q->where('status', 'completed'); }
    public function scopeInProgress($q) { return $q->where('status', 'in_progress'); }

    // ========== Helpers ==========
    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isAccepted(): bool   { return $this->status === 'accepted'; }
    public function isInProgress(): bool { return $this->status === 'in_progress'; }
    public function isCompleted(): bool  { return $this->status === 'completed'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }

    // ========== Accessor: Badge ==========
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'     => '<span class="badge badge-pending">قيد الانتظار</span>',
            'accepted'    => '<span class="badge badge-approved">تم القبول</span>',
            'in_progress' => '<span class="badge badge-progress">جاري التنفيذ</span>',
            'completed'   => '<span class="badge badge-completed">مكتمل</span>',
            'cancelled'   => '<span class="badge badge-cancelled">ملغي</span>',
            default       => '',
        };
    }

    // ========== Accessor: مشكلة الصورة ==========
    public function getProblemImageUrlAttribute(): ?string
    {
        return $this->problem_image ? url('storage/' . $this->problem_image) : null;
    }
        // عدد العروض
    public function getBidsCountAttribute(): int
    {
        return $this->bids()->pending()->count();
    }

    // هل الطلب قابل لتلقي عروض؟
    public function isOpenForBidding(): bool
    {
        return $this->status === 'pending' && is_null($this->craftsman_id);
    }

}
