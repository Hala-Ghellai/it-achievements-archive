<?php

namespace App\Http\Controllers;

use App\Models\Department;

class HomeController extends Controller
{
    /**
     * Home page: hero + live statistics + department cards.
     */
    public function index()
    {
        $year = now()->year;

        $departments = Department::query()
            ->withCount(['achievements as achievements_count' => fn ($q) => $q->whereYear('achievement_at', $year)])
            ->orderBy('sort_order')
            ->get();

        $totalAchievements = Department::query()
            ->withCount('achievements')
            ->get()
            ->sum('achievements_count');

        $yearAchievements = $departments->sum('achievements_count');

        $stats = [
            'founding_year' => (int) config('site.founding_year'),
            'organization_name' => config('site.organization_name'),
            'department_name' => config('site.department_name'),
            'total_count' => $totalAchievements,
            'year_count' => $yearAchievements,
        ];

        return view('home', [
            'stats' => $stats,
            'departments' => $departments,
            'year' => $year,
        ]);
    }
}
