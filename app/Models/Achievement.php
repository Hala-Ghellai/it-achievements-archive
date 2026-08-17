<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'title',
        'description',
        'achievement_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'achievement_at' => 'datetime',
        ];
    }

    /** An achievement belongs to a department. */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** The employee who logged the achievement (Admin/staff). */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** The employees who worked on this achievement (many-to-many). */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class)->withTimestamps();
    }

    /** Attached media (images, videos, PDFs). */
    public function media(): HasMany
    {
        return $this->hasMany(AchievementMedia::class)->orderBy('sort_order');
    }

    /** The first image, used as a thumbnail in list views. */
    public function thumbnailMedia(): ?AchievementMedia
    {
        return $this->media()->where('type', 'image')->first();
    }

    /** Number of attached images. */
    public function imagesCount(): int
    {
        return $this->media()->where('type', 'image')->count();
    }
}
