<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Roles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasDefaultTenant, HasTenants
{
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Roles::Admin);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin' && $this->hasRole(Roles::Admin)) {
            return true;
        }

        if ($panel->getId() === 'app' && ($this->hasRole(Roles::Client) || $this->hasRole(Roles::Admin))) {
            return true;
        }

        return false;
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->wherePivot('invitation_accepted_at', '!=', null);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->hasRole(Roles::Admin)) {
            return true;
        }

        $team = $this->team()->whereKey($tenant)->first();
        if (empty($team)) {
            return false;
        }
        $invitation_accepted_at = $team->pivot->invitation_accepted_at;

        return ! empty($invitation_accepted_at);
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->latestTeam;
    }

    public function latestTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'latest_team_id');
    }

    public function team(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot('invitation_accepted_at', 'code', 'last_invitation_send_at');
    }
}
