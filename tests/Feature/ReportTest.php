<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private Department $networks;

    private Department $security;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->networks = Department::create(['slug' => 'networks', 'name' => 'قسم الشبكات', 'description' => '-', 'sort_order' => 1]);
        $this->security = Department::create(['slug' => 'security', 'name' => 'قسم الأمن', 'description' => '-', 'sort_order' => 2]);

        $this->admin = User::create([
            'username' => 'admin',
            'full_name' => 'Site Admin',
            'role' => User::ROLE_ADMIN,
            'password' => 'admin123',
        ]);

        Achievement::create(['department_id' => $this->networks->id, 'title' => 'Q1 networks win', 'description' => 'x', 'achievement_at' => '2026-02-10 10:00']);
        Achievement::create(['department_id' => $this->networks->id, 'title' => 'Q3 networks win', 'description' => 'x', 'achievement_at' => '2026-08-01 10:00']);
        Achievement::create(['department_id' => $this->security->id, 'title' => 'Q1 security win', 'description' => 'x', 'achievement_at' => '2026-03-31 23:00']);
    }

    public function test_guests_cannot_open_reports(): void
    {
        $this->get(route('reports.index'))->assertRedirect(route('login'));
        $this->get(route('reports.show', ['period' => 'year', 'year' => 2026]))->assertRedirect(route('login'));
    }

    public function test_report_builder_page_loads(): void
    {
        $this->actingAs($this->admin)->get(route('reports.index'))
            ->assertOk()
            ->assertSee('ربع سنوي')
            ->assertSee('قسم الشبكات');
    }

    public function test_quarterly_report_for_all_departments_includes_only_that_quarter(): void
    {
        $this->actingAs($this->admin)
            ->get(route('reports.show', ['period' => 'quarter', 'year' => 2026, 'quarter' => 1, 'department' => 'all']))
            ->assertOk()
            ->assertSee('التقرير الربع سنوي')
            ->assertSee('ت.م/2026/ر1/الكل')
            ->assertSee('Q1 networks win')
            ->assertSee('Q1 security win')
            ->assertDontSee('Q3 networks win');
    }

    public function test_half_year_report_for_one_department(): void
    {
        $this->actingAs($this->admin)
            ->get(route('reports.show', ['period' => 'half', 'year' => 2026, 'half' => 2, 'department' => 'networks']))
            ->assertOk()
            ->assertSee('التقرير النصف سنوي')
            ->assertSee('ت.م/2026/ن2/شبكات')
            ->assertSee('Q3 networks win')
            ->assertDontSee('Q1 networks win')
            ->assertDontSee('Q1 security win');
    }

    public function test_annual_report_counts_every_achievement_of_the_year(): void
    {
        $this->actingAs($this->admin)
            ->get(route('reports.show', ['period' => 'year', 'year' => 2026]))
            ->assertOk()
            ->assertSee('التقرير السنوي')
            ->assertSee('Q1 networks win')
            ->assertSee('Q3 networks win')
            ->assertSee('Q1 security win');
    }

    public function test_team_names_are_optional(): void
    {
        $achievement = Achievement::where('title', 'Q1 networks win')->first();
        $achievement->employees()->create(['full_name' => 'Ahmed Engineer']);

        $params = ['period' => 'quarter', 'year' => 2026, 'quarter' => 1, 'department' => 'networks'];

        $this->actingAs($this->admin)
            ->get(route('reports.show', $params + ['team' => 1]))
            ->assertOk()
            ->assertSee('فريق العمل')
            ->assertSee('Ahmed Engineer');

        $this->actingAs($this->admin)
            ->get(route('reports.show', $params))
            ->assertOk()
            ->assertDontSee('Ahmed Engineer')
            ->assertDontSee('<th style="width: 150px">فريق العمل</th>', false);
    }

    public function test_report_downloads_as_word_document(): void
    {
        if (! class_exists(\ZipArchive::class)) {
            $this->markTestSkipped('PHP zip extension is not enabled.');
        }

        $this->actingAs($this->admin)
            ->get(route('reports.word', ['period' => 'quarter', 'year' => 2026, 'quarter' => 1, 'team' => 1]))
            ->assertOk()
            ->assertDownload('IT-2026-Q1-ALL.docx');
    }

    public function test_pdf_option_opens_the_print_dialog(): void
    {
        $this->actingAs($this->admin)
            ->get(route('reports.show', ['period' => 'year', 'year' => 2026, 'print' => 1]))
            ->assertOk()
            ->assertSee('setTimeout(savePdf', false);
    }

    public function test_invalid_period_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->get(route('reports.show', ['period' => 'weekly', 'year' => 2026]))
            ->assertSessionHasErrors('period');
    }
}
