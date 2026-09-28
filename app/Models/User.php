<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'full_name', 'email', 'phone', 'password', 'role', 'avatar',
        'is_verified', 'is_active', 'remember_token',
        'loyalty_points', 'total_points_earned', 'total_points_spent',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_verified'    => 'boolean',
        'loyalty_points' => 'integer',
    ];

    // ========== Accessors ==========
    public function getAvatarUrlAttribute(): string
    {
        if (empty($this->avatar) || $this->avatar === 'default-avatar.png') {
            return $this->avatarFallback();
        }
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }
        if (Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }
        return $this->avatarFallback();
    }

    private function avatarFallback(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name)
             . '&background=1A365D&color=fff&size=128&bold=true';
    }

    // ========== Relationships ==========
    public function craftsmanProfile()  { return $this->hasOne(CraftsmanProfile::class); }
    public function wallet()            { return $this->hasOne(CraftsmanWallet::class, 'craftsman_id'); }
    public function clientRequests()    { return $this->hasMany(Request::class, 'client_id'); }
    public function craftsmanRequests() { return $this->hasMany(Request::class, 'craftsman_id'); }
    public function notifications()     { return $this->hasMany(Notification::class); }
    public function reviewsReceived()   { return $this->hasMany(Review::class, 'craftsman_id'); }
    public function reviewsGiven()      { return $this->hasMany(Review::class, 'client_id'); }
    public function walletTransactions(){ return $this->hasMany(WalletTransaction::class, 'craftsman_id'); }
    public function loginLogs()         { return $this->hasMany(LoginLog::class); }
    public function rewards()           { return $this->hasMany(Reward::class); }

    // ========== Scopes ==========
    public function scopeClients($q)   { return $q->where('role', 'client'); }
    public function scopeCraftsmen($q) { return $q->where('role', 'craftsman'); }
    public function scopeAdmins($q)    { return $q->where('role', 'admin'); }
    public function scopeActive($q)    { return $q->where('is_active', true); }

    // ========== Helpers ==========
    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isCraftsman(): bool { return $this->role === 'craftsman'; }
    public function isClient(): bool    { return $this->role === 'client'; }
        public function publicProfileUrl(): string
    {
        $slug = \Illuminate\Support\Str::slug($this->full_name) ?: 'craftsman';
        return url("/profile/{$this->id}-{$slug}");
    }

}
