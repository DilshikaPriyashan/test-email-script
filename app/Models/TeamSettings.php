<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_name',
        'from_email',
        'smtp_host',
        'smtp_encryption',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'team_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    protected function casts(): array
    {
        return [
            'smtp_host' => 'encrypted',
            'smtp_username' => 'encrypted',
            'smtp_password' => 'encrypted',
        ];
    }
}
