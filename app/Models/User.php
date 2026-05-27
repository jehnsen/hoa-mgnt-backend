<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, MustVerifyEmail, Notifiable, SoftDeletes;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class,
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_user')
                    ->withPivot(['move_in_at', 'move_out_at', 'is_primary_resident', 'is_owner'])
                    ->withTimestamps();
    }

    public function vendorProfile(): HasOne
    {
        return $this->hasOne(VendorProfile::class);
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function boardPositions(): HasMany
    {
        return $this->hasMany(BoardPosition::class);
    }

    public function activeBoardPosition(): HasOne
    {
        return $this->hasOne(BoardPosition::class)->where('is_active', true)->latestOfMany();
    }

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }

    public function grantedProxies(): HasMany
    {
        return $this->hasMany(MeetingProxy::class, 'grantor_id');
    }

    public function receivedProxies(): HasMany
    {
        return $this->hasMany(MeetingProxy::class, 'proxy_id');
    }

    // ─── Domain Helpers ───────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isBoardMember(): bool
    {
        return $this->role === UserRole::BoardMember;
    }

    public function isResident(): bool
    {
        return $this->role === UserRole::Resident;
    }

    public function isVendor(): bool
    {
        return $this->role === UserRole::Vendor;
    }

    public function canManageFinancials(): bool
    {
        return $this->role->canManageFinancials();
    }

    public function canManageViolations(): bool
    {
        return $this->role->canManageViolations();
    }
}
