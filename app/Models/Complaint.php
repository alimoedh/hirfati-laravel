<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'request_id', 'client_id', 'craftsman_id', 'reason', 'details',
        'evidence_image', 'status', 'admin_response', 'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function request()   { return $this->belongsTo(Request::class); }
    public function client()    { return $this->belongsTo(User::class, 'client_id'); }
    public function craftsman() { return $this->belongsTo(User::class, 'craftsman_id'); }

    public function getEvidenceImageUrlAttribute(): ?string
    {
        return $this->evidence_image ? url('storage/' . $this->evidence_image) : null;
    }

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'quality'  => 'جودة الخدمة',
            'delay'    => 'تأخر الموعد',
            'price'    => 'خلاف على السعر',
            'behavior' => 'سلوك غير لائق',
            'other'    => 'سبب آخر',
            default    => $this->reason,
        };
    }
}
