<?php

namespace App\Models;

use Database\Factories\ContentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Content extends Model
{
    /** @use HasFactory<ContentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
