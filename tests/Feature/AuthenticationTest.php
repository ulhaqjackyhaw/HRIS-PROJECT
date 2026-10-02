<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');
        $response->assertSee('admin@hris.corp');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@hris.corp',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@hris.corp',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('portal'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@hris.corp',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', [
            'email' => 'admin@hris.corp',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_module_portal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('portal'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Modul Operasional SDM');
        $response->assertSee('Core HR & Kepegawaian');
        $response->assertSee('Recruitment & ATS');
        $response->assertSee('Attendance & Waktu Kerja');
        $response->assertSee('Payroll & Kompensasi');
    }

    public function test_authenticated_user_can_view_module_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('modules.show', 'payroll'));

        $response->assertStatus(200);
        $response->assertSee('Payroll & Kompensasi');
        $response->assertSee('Kalkulasi PPh 21');
    }
}
