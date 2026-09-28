<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'icon', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function craftsmanProfiles() { return $this->hasMany(CraftsmanProfile::class); }
    public function requests()          { return $this->hasMany(Request::class); }
    public function tutorials()         { return $this->hasMany(Tutorial::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }
}
