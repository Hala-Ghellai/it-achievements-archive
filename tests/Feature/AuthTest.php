<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\AchievementMedia;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private Department $networks;

    private Department $security;

    protected function setUp(): void
    {
        parent::setUp();

        $this->networks = Department::create([
            'slug' => 'networks',
            'name' => 'Networks & Infrastructure',
            'description' => 'Network management',
        ]);

        $this->security = Department::create([
            'slug' => 'security',
            'name' => 'Information Security',
            'description' => 'Security operations',
        ]);

        User::create([
            'username' => 'admin',
            'full_name' => 'Site Admin',
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
            'password' => 'admin123',
        ]);

        User::create([
            'username' => 'net_admin',
            'full_name' => 'Network Admin',
            'role' => User::ROLE_STAFF,
            'department_id' => $this->networks->id,
            'password' => 'net123',
        ]);
    }

    public function test_guest_can_view_home_and_login_pages(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('تسجيل الدخول');
        $this->get(route('login'))->assertOk();
        $this->get('/register')->assertNotFound();
    }

    public function test_user_can_log_in_with_username(): void
    {
        $response = $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs(User::where('username', 'admin')->first());
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_user_can_log_out(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_admin_can_create_achievements_in_any_department(): void
    {
        $admin = User::where('username', 'admin')->first();

        $this->actingAs($admin);

        $this->assertTrue(Gate::allows('create-achievements'));
        $this->assertTrue(Gate::allows('create-achievement-in', $this->networks));
        $this->assertTrue(Gate::allows('create-achievement-in', $this->security));
    }

    public function test_staff_can_only_create_achievements_in_own_department(): void
    {
        $staff = User::where('username', 'net_admin')->first();

        $this->actingAs($staff);

        $this->assertTrue(Gate::allows('create-achievements'));
        $this->assertTrue(Gate::allows('create-achievement-in', $this->networks));
        $this->assertFalse(Gate::allows('create-achievement-in', $this->security));
    }

    public function test_staff_can_only_manage_achievements_in_own_department(): void
    {
        $staff = User::where('username', 'net_admin')->first();

        $own = Achievement::create([
            'department_id' => $this->networks->id,
            'title' => 'Core switch upgrade',
            'description' => 'Replaced core switching infrastructure.',
            'achievement_at' => now(),
            'created_by' => $staff->id,
        ]);

        $other = Achievement::create([
            'department_id' => $this->security->id,
            'title' => 'SOC deployment',
            'description' => 'Established the security operations centre.',
            'achievement_at' => now(),
        ]);

        $this->actingAs($staff);

        $this->assertTrue(Gate::allows('manage-achievement', $own));
        $this->assertFalse(Gate::allows('manage-achievement', $other));
    }

    public function test_guest_cannot_access_create_achievement(): void
    {
        $this->get(route('achievements.create'))->assertRedirect(route('login'));
        $this->post(route('achievements.store'), [
            'title' => 'Guest attempt',
            'department_id' => $this->networks->id,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('achievements', 0);
    }

    public function test_staff_can_create_achievement_in_own_department_with_media(): void
    {
        Storage::fake('public');

        $staff = User::where('username', 'net_admin')->first();

        $response = $this->actingAs($staff)->post(route('achievements.store'), [
            'title' => 'Core upgrade',
            'description' => 'Upgraded the core switching infrastructure.',
            'achievement_at' => '2026-03-01T10:00',
            'department_id' => $this->networks->id,
            'thumbnail' => UploadedFile::fake()->image('cover.jpg'),
            'images' => [UploadedFile::fake()->image('a.png'), UploadedFile::fake()->image('b.png')],
            'videos' => [UploadedFile::fake()->create('clip.mp4', 2000, 'video/mp4')],
            'pdfs' => [UploadedFile::fake()->create('report.pdf', 2000, 'application/pdf')],
            'employees' => "م. أحمد\nم. فاطمة",
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('achievements', [
            'title' => 'Core upgrade',
            'department_id' => $this->networks->id,
            'created_by' => $staff->id,
        ]);

        $achievement = Achievement::where('title', 'Core upgrade')->first();
        $this->assertDatabaseCount('achievement_media', 5);
        $this->assertSame(AchievementMedia::TYPE_IMAGE, $achievement->media()->where('sort_order', 0)->value('type'));

        $this->assertSame(2, $achievement->employees()->count());
    }

    public function test_staff_cannot_create_achievement_in_other_department(): void
    {
        $staff = User::where('username', 'net_admin')->first();

        $response = $this->actingAs($staff)->post(route('achievements.store'), [
            'title' => 'Unauthorized',
            'description' => 'Should not be allowed.',
            'achievement_at' => '2026-03-01T10:00',
            'department_id' => $this->security->id,
        ]);

        $response->assertSessionHasErrors('department_id');
        $this->assertDatabaseCount('achievements', 0);
    }

    public function test_admin_can_create_achievement_in_any_department(): void
    {
        Storage::fake('public');

        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->post(route('achievements.store'), [
            'title' => 'SOC deployment',
            'description' => 'Established the security operations centre.',
            'achievement_at' => '2026-03-01T10:00',
            'department_id' => $this->security->id,
            'thumbnail' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('achievements', [
            'title' => 'SOC deployment',
            'department_id' => $this->security->id,
            'created_by' => $admin->id,
        ]);
    }
}
