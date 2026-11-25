<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'color',
        'sort_order',
    ];

    /** A department has many achievements. */
    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    /** Total number of achievements for this department. */
    public function achievementsCount(): int
    {
        return $this->achievements()->count();
    }

    /** Number of achievements published in a given year. */
    public function achievementsCountInYear(int $year): int
    {
        return $this->achievements()
            ->whereYear('achievement_at', $year)
            ->count();
    }

    /** Arabic phrase for a number of works (أعمال), e.g. "عملان" or "5 أعمال". */
    public static function countLabel(int $count): string
    {
        return match (true) {
            $count === 0 => 'لا توجد أعمال',
            $count === 1 => 'عمل واحد',
            $count === 2 => 'عملان',
            $count <= 10 => $count.' أعمال',
            default => $count.' عملاً',
        };
    }
}
