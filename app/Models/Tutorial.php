<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $fillable = ['category_id', 'title', 'description', 'video_url', 'thumbnail', 'views', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function category()      { return $this->belongsTo(Category::class); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
