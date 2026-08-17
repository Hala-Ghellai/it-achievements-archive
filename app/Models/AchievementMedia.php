<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchievementMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'achievement_id',
        'type',
        'source',
        'url',
        'original_name',
        'sort_order',
    ];

    public const TYPE_IMAGE = 'image';

    public const TYPE_VIDEO = 'video';

    public const TYPE_PDF = 'pdf';

    public const SOURCE_UPLOAD = 'upload';

    public const SOURCE_YOUTUBE = 'youtube';

    public const SOURCE_VIMEO = 'vimeo';

    /** A media item belongs to an achievement. */
    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }
}
