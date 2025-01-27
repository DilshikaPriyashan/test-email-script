<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function teamSettings(): HasOne
    {
        return $this->hasOne(TeamSettings::class);
    }

    public function aPIKeys(): HasOne
    {
        return $this->hasOne(APIKey::class);
    }
}
