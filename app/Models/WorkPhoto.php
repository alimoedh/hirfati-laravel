<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPhoto extends Model
{
    protected $fillable = [
        'request_id', 'type', 'image_path', 'description', 'uploaded_by',
    ];

    // ═══════════ Relationships ═══════════
    public function request()    { return $this->belongsTo(Request::class); }
    public function uploader()   { return $this->belongsTo(User::class, 'uploaded_by'); }

    // ═══════════ Scopes ═══════════
    public function scopeBefore($q) { return $q->where('type', 'before'); }
    public function scopeAfter($q)  { return $q->where('type', 'after'); }

    // ═══════════ Accessor ═══════════
    public function getImageUrlAttribute(): string
    {
        return url('storage/' . $this->image_path);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'before' ? 'قبل التنفيذ' : 'بعد التنفيذ';
    }
}
