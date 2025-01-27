<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'api_ref',
        'attributes',
        'content',
        'auth_mechanism',
        'strict_mode',
        'to',
        'cc',
        'bcc',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'to' => 'array',
            'cc' => 'array',
            'bcc' => 'array',
        ];
    }
}
