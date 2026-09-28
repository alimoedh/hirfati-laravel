<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CraftsmanProfile extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'experience_years', 'bio',
        'identity_document', 'hourly_rate', 'is_approved', 'is_available',
        'is_emergency', 'emergency_phone', 'rating_avg', 'total_reviews',
        'latitude', 'longitude',
    ];

    protected $casts = [
        'is_approved'  => 'boolean',
        'is_available' => 'boolean',
        'is_emergency' => 'boolean',
        'rating_avg'   => 'decimal:2',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }

    public function scopeApproved($q)  { return $q->where('is_approved', true); }
    public function scopeAvailable($q) { return $q->where('is_available', true); }
    public function scopeEmergency($q) { return $q->where('is_emergency', true); }
}
