<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['member', 'president', 'vice_president', 'secretary_general', 'admin'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    private function president(): User
    {
        $user = User::factory()->create([
            'is_suspended' => false,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('president');

        return $user;
    }

    private function member(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'is_suspended' => false,
            'email_verified_at' => now(),
        ], $attributes));
        $user->assignRole('member');

        return $user;
    }

    public function test_members_dashboard_renders_manage_dialog_triggers_without_native_dialogs(): void
    {
        $president = $this->president();
        $target = $this->member(['name' => 'Target Member']);

        $response = $this->actingAs($president)->get(route('members.index'));

        $response->assertOk()
            ->assertSee('Target Member')
            ->assertSee('data-manage-member', false)
            ->assertSee('data-edit-url=', false)
            ->assertSee('data-promote-url=', false)
            ->assertSee('data-delete-url=', false)
            ->assertSee('TGD.menu', false)
            ->assertSee('TGD.confirm', false)
            // Old dropdown and native dialogs must be gone
            ->assertDontSee('data-action=', false)
            ->assertDontSee('menu-panel nb-menu', false)
            ->assertDontSee('window.confirm', false)
            ->assertDontSee('window.alert', false);
    }

    public function test_president_can_promote_member_via_json(): void
    {
        $president = $this->president();
        $target = $this->member();

        // Promote to president
        $response = $this->actingAs($president)
            ->postJson(route('members.promote', $target), ['role' => 'president']);

        $response->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue($target->fresh()->hasRole('president'));
    }

    public function test_president_can_suspend_and_unsuspend_via_json(): void
    {
        $president = $this->president();
        $target = $this->member();

        // Suspend
        $response = $this->actingAs($president)
            ->postJson(route('members.suspend', $target), ['reason' => 'Policy violation']);

        $response->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue($target->fresh()->isSuspended());

        // Unsuspend
        $response = $this->actingAs($president)
            ->postJson(route('members.unsuspend', $target));

        $response->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertFalse($target->fresh()->isSuspended());
    }

    public function test_president_can_delete_member_via_json(): void
    {
        $president = $this->president();
        $target = $this->member();

        $response = $this->actingAs($president)
            ->deleteJson(route('members.destroy', $target));

        $response->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue($target->fresh()->trashed());
    }
}
