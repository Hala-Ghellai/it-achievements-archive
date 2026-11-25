<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AchievementMedia;
use App\Models\Department;
use App\Support\ReportDocx;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Periodic work (achievement) reports (quarterly / semi-annual / annual),
 * for one department or for all departments together.
 */
class ReportController extends Controller
{
    public const PERIOD_QUARTER = 'quarter';

    public const PERIOD_HALF = 'half';

    public const PERIOD_YEAR = 'year';

    private const QUARTER_NAMES = [1 => 'الربع الأول', 2 => 'الربع الثاني', 3 => 'الربع الثالث', 4 => 'الربع الرابع'];

    private const HALF_NAMES = [1 => 'النصف الأول', 2 => 'النصف الثاني'];

    /** Report builder: choose period, year and department. */
    public function index(): View
    {
        $currentYear = now()->year;
        $firstYear = (int) (Achievement::min('achievement_at')
            ? CarbonImmutable::parse(Achievement::min('achievement_at'))->year
            : $currentYear);

        return view('reports.index', [
            'departments' => Department::orderBy('sort_order')->get(),
            'years' => range($currentYear, min($firstYear, $currentYear)),
            'currentYear' => $currentYear,
            'currentQuarter' => (int) ceil(now()->month / 3),
            'currentHalf' => now()->month <= 6 ? 1 : 2,
            'quarterNames' => self::QUARTER_NAMES,
            'halfNames' => self::HALF_NAMES,
        ]);
    }

    /** The printable, official report (also used to save as PDF). */
    public function show(Request $request): View
    {
        return view('reports.show', $this->buildReport($request) + [
            'autoPrint' => $request->boolean('print'),
        ]);
    }

    /** The same report as an editable Word (.docx) file. */
    public function word(Request $request): BinaryFileResponse
    {
        abort_unless(class_exists(\ZipArchive::class), 500, 'تصدير Word يتطلب تفعيل إضافة PHP zip على الخادم.');

        $report = $this->buildReport($request);
        $path = (new ReportDocx($report))->save();

        return response()
            ->download($path, $report['fileName'].'.docx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])
            ->deleteFileAfterSend();
    }

    /**
     * Validate the request and collect everything a report needs.
     *
     * @return array<string, mixed>
     */
    private function buildReport(Request $request): array
    {
        $data = $request->validate([
            'period' => ['required', Rule::in([self::PERIOD_QUARTER, self::PERIOD_HALF, self::PERIOD_YEAR])],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'quarter' => ['nullable', 'integer', 'between:1,4'],
            'half' => ['nullable', 'integer', 'between:1,2'],
            'department' => ['nullable', 'string'],
            'images' => ['nullable', 'boolean'],
            'team' => ['nullable', 'boolean'],
        ], [
            'period.required' => 'يرجى اختيار نوع التقرير.',
            'year.required' => 'يرجى اختيار السنة.',
        ]);

        $year = (int) $data['year'];
        [$start, $end, $periodName, $periodCode] = $this->resolvePeriod(
            $data['period'],
            $year,
            (int) ($data['quarter'] ?? 1),
            (int) ($data['half'] ?? 1),
        );

        $selectedDepartment = null;
        if (! empty($data['department']) && $data['department'] !== 'all') {
            $selectedDepartment = Department::where('slug', $data['department'])->firstOrFail();
        }

        $departments = $selectedDepartment
            ? collect([$selectedDepartment])
            : Department::orderBy('sort_order')->get();

        $withImages = (bool) ($data['images'] ?? false);
        $withTeam = (bool) ($data['team'] ?? false);

        $achievements = Achievement::query()
            ->whereIn('department_id', $departments->pluck('id'))
            ->whereBetween('achievement_at', [$start, $end])
            ->with([
                'employees',
                'media' => fn ($q) => $q->where('type', AchievementMedia::TYPE_IMAGE),
            ])
            ->orderBy('achievement_at')
            ->get()
            ->groupBy('department_id');

        $sections = $departments->map(fn (Department $department) => [
            'department' => $department,
            'achievements' => $achievements->get($department->id, collect()),
        ]);

        $total = $sections->sum(fn ($section) => $section['achievements']->count());

        $reportTypeName = match ($data['period']) {
            self::PERIOD_QUARTER => 'التقرير الربع سنوي',
            self::PERIOD_HALF => 'التقرير النصف سنوي',
            default => 'التقرير السنوي',
        };

        // Arabic report number shown on the document, e.g. "ت.م/2026/ر3/الكل".
        $reportNumber = implode('/', [
            'ت.م',
            $year,
            strtr($periodCode, ['Q' => 'ر', 'H' => 'ن', 'Y' => 'س']),
            $selectedDepartment ? $this->shortDepartmentName($selectedDepartment) : 'الكل',
        ]);

        // ASCII version, used for downloaded file names.
        $fileName = sprintf(
            'IT-%d-%s-%s',
            $year,
            $periodCode,
            $selectedDepartment ? strtoupper($selectedDepartment->slug) : 'ALL'
        );

        return [
            'reportTypeName' => $reportTypeName,
            'periodName' => $periodName,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'selectedDepartment' => $selectedDepartment,
            'sections' => $sections,
            'total' => $total,
            'withImages' => $withImages,
            'withTeam' => $withTeam,
            'reportNumber' => $reportNumber,
            'fileName' => $fileName,
            'issuedAt' => now(),
            'issuedBy' => $request->user(),
        ];
    }

    /** "قسم الشبكات والدعم الفني" → "شبكات" (first word, without "قسم" and "ال"). */
    private function shortDepartmentName(Department $department): string
    {
        $words = preg_split('/\s+/u', trim(preg_replace('/^قسم\s+/u', '', $department->name)));
        $first = $words[0] ?? $department->slug;

        return preg_replace('/^ال(?=\X{2,})/u', '', $first);
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable, 2: string, 3: string}
     */
    private function resolvePeriod(string $period, int $year, int $quarter, int $half): array
    {
        $yearStart = CarbonImmutable::create($year, 1, 1)->startOfDay();

        return match ($period) {
            self::PERIOD_QUARTER => [
                $yearStart->addMonths(($quarter - 1) * 3),
                $yearStart->addMonths(($quarter - 1) * 3)->endOfQuarter(),
                self::QUARTER_NAMES[$quarter].' لعام '.$year,
                'Q'.$quarter,
            ],
            self::PERIOD_HALF => [
                $yearStart->addMonths(($half - 1) * 6),
                $yearStart->addMonths(($half - 1) * 6 + 5)->endOfMonth(),
                self::HALF_NAMES[$half].' لعام '.$year,
                'H'.$half,
            ],
            default => [
                $yearStart,
                $yearStart->endOfYear(),
                'عام '.$year,
                'Y',
            ],
        };
    }
}
