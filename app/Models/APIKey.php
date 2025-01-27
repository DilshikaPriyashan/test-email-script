<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class APIKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'team_id',
    ];

    public function generateApiToken()
    {
        return 'SK-'.$this->id.'-'.Str::upper(Str::random(4)).'-'.
            Str::upper(Str::random(4)).'-'.
            Str::upper(Str::random(4)).'-'.
            Str::upper(Str::random(4));
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->key = $model->generateApiToken();
            $model->key_rotated_at = Carbon::now();
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function createBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'key' => 'encrypted',
        ];
    }
}
