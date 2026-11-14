<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuspensionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['member', 'president', 'vice_president', 'secretary_general', 'team_lead'] as $r) {
            Role::findOrCreate($r, 'web');
        }
    }

    private function president(): User
    {
        $u = User::factory()->create([
            'is_suspended' => false,
            'email_verified_at' => now(),
        ]);
        $u->assignRole('president');

        return $u;
    }

    private function member(): User
    {
        $u = User::factory()->create([
            'is_suspended' => false,
            'email_verified_at' => now(),
            'name' => 'Debater Jane',
            'team' => 'english',
        ]);
        $u->assignRole('member');

        return $u;
    }

    public function test_suspended_member_is_redirected_to_suspend_page_from_protected_routes(): void
    {
        $member = $this->member();
        $member->suspend('Repeated unexcused tournament absence');

        // Accessing dashboard
        $response = $this->actingAs($member)->get(route('dashboard'));
        $response->assertRedirect(route('suspended'));

        // Accessing events
        $response = $this->actingAs($member)->get(route('events.index'));
        $response->assertRedirect(route('suspended'));

        // Accessing profile
        $response = $this->actingAs($member)->get(route('profile.edit'));
        $response->assertRedirect(route('suspended'));
    }

    public function test_suspend_page_renders_with_member_details_and_president_reason(): void
    {
        $member = $this->member();
        $reason = 'Violation of debate etiquette code during national finals';
        $member->suspend($reason);

        $response = $this->actingAs($member)->get(route('suspended'));

        $response->assertOk();
        $response->assertSee('Account Suspended');
        $response->assertSee('Debater Jane');
        $response->assertSee($reason);
        $response->assertSee('English team');
        $response->assertSee('Sign out');
    }

    public function test_active_member_visiting_suspended_route_is_redirected_to_dashboard(): void
    {
        $member = $this->member();

        $response = $this->actingAs($member)->get(route('suspended'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_suspended_member_logging_in_is_redirected_to_suspended_page(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => 'Disciplinary hold',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('suspended'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_president_suspending_and_unsuspending_member(): void
    {
        $president = $this->president();
        $member = $this->member();

        // President suspends member
        $this->actingAs($president)
            ->post(route('members.suspend', $member), ['reason' => 'Misconduct in assembly']);

        $this->assertTrue($member->fresh()->isSuspended());
        $this->assertSame('Misconduct in assembly', $member->fresh()->suspension_reason);

        // Member cannot access dashboard
        $this->actingAs($member->fresh())
            ->get(route('dashboard'))
            ->assertRedirect(route('suspended'));

        // President unsuspends member
        $this->actingAs($president)
            ->post(route('members.unsuspend', $member));

        $this->assertFalse($member->fresh()->isSuspended());
        $this->assertNull($member->fresh()->suspension_reason);

        // Member can access dashboard again
        $this->actingAs($member->fresh())
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_json_request_by_suspended_user_returns_403(): void
    {
        $member = $this->member();
        $member->suspend('API access restricted');

        $response = $this->actingAs($member)
            ->getJson(route('events.index'));

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Your account has been suspended.']);
    }

    public function test_guest_can_render_account_suspended_page(): void
    {
        $response = $this->get(route('suspended.guest'));
        $response->assertOk();
        $response->assertSee('Account Suspended');
    }
}
