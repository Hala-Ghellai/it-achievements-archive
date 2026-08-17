<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AchievementMedia;
use App\Models\Department;

class DepartmentController extends Controller
{
    /**
     * Show a single department with its achievements (read-only for everyone).
     */
    public function show(Department $department)
    {
        $year = now()->year;

        $achievements = $department->achievements()
            ->with([
                'department',
                'media' => fn ($q) => $q->where('type', AchievementMedia::TYPE_IMAGE)->limit(1),
            ])
            ->withCount([
                'media as images_count' => fn ($q) => $q->where('type', AchievementMedia::TYPE_IMAGE),
            ])
            ->orderByDesc('achievement_at')
            ->get()
            ->map(function (Achievement $achievement) {
                $achievement->thumbnail = $achievement->media->first()?->url;

                return $achievement;
            });

        return view('departments.show', [
            'department' => $department,
            'achievements' => $achievements,
            'year' => $year,
        ]);
    }
}
