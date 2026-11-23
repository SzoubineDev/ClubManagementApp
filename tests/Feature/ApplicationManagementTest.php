<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRecrute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApplicationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['member', 'president', 'vice_president', 'secretary_general', 'admin', 'team_lead'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    private function manager(string $role = 'president'): User
    {
        $user = User::factory()->create([
            'is_suspended' => false,
            'email_verified_at' => now(),
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function member(): User
    {
        $user = User::factory()->create([
            'is_suspended' => false,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('member');

        return $user;
    }

    private function sectionLead(string $team): User
    {
        $user = User::factory()->create([
            'is_suspended' => false,
            'email_verified_at' => now(),
            'team' => $team,
        ]);
        $user->assignRole('team_lead');

        return $user;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function application(array $attributes = []): UserRecrute
    {
        static $n = 0;
        $n++;

        return UserRecrute::create(array_merge([
            'first_name' => 'Applicant',
            'last_name' => 'Number'.$n,
            'phone_number' => '06000000'.str_pad((string) $n, 2, '0', STR_PAD_LEFT),
            'email' => "applicant{$n}@example.com",
            'cne' => 'CNE'.str_pad((string) $n, 5, '0', STR_PAD_LEFT),
            'filiere' => 'Cycle Ingénieur',
            'preferred_section' => 'EN',
            'motivation' => 'I want to debate.',
        ], $attributes));
    }

    // --- Database ----------------------------------------------------------

    public function test_status_column_exists_with_pending_default(): void
    {
        $this->assertTrue(
            Schema::hasColumn('users_recrute', 'status'),
            'users_recrute should have a status column.'
        );

        // Insert without touching `status` (simulates a pre-migration row).
        DB::table('users_recrute')->insert([
            'first_name' => 'Legacy',
            'last_name' => 'Applicant',
            'phone_number' => '0611111111',
            'email' => 'legacy@example.com',
            'cne' => 'LEGACY001',
            'filiere' => 'DEUST',
            'preferred_section' => 'FR',
            'motivation' => 'Legacy row.',
        ]);

        $this->assertSame('pending', DB::table('users_recrute')->where('cne', 'LEGACY001')->value('status'));
    }

    // --- Access control ----------------------------------------------------

    public function test_manager_can_load_the_dashboard_with_real_records(): void
    {
        $application = $this->application(['first_name' => 'Sara', 'last_name' => 'El Amrani']);

        $response = $this->actingAs($this->manager())->get(route('applications.index'));

        $response->assertOk();
        $response->assertSee('Sara El Amrani');
        $response->assertSee($application->email);
        $response->assertSee($application->cne);
    }

    public function test_each_manager_role_can_access_the_dashboard(): void
    {
        foreach (['president', 'vice_president', 'secretary_general', 'admin'] as $role) {
            $this->actingAs($this->manager($role))
                ->get(route('applications.index'))
                ->assertOk();
        }
    }

    public function test_non_manager_is_forbidden_from_the_dashboard(): void
    {
        $this->actingAs($this->member())
            ->get(route('applications.index'))
            ->assertForbidden();
    }

    public function test_section_lead_sees_only_applications_from_their_section(): void
    {
        $this->application(['first_name' => 'English', 'preferred_section' => 'EN']);
        $this->application(['first_name' => 'French', 'preferred_section' => 'FR']);
        $this->application(['first_name' => 'Arabic', 'preferred_section' => 'AR']);

        $this->actingAs($this->sectionLead('english'))
            ->get(route('applications.index'))
            ->assertOk()
            ->assertSee('English')
            ->assertDontSee('French')
            ->assertDontSee('Arabic');

        $this->actingAs($this->sectionLead('french'))
            ->get(route('applications.index'))
            ->assertOk()
            ->assertSee('French')
            ->assertDontSee('English')
            ->assertDontSee('Arabic');

        $this->actingAs($this->sectionLead('arabic'))
            ->get(route('applications.index'))
            ->assertOk()
            ->assertSee('Arabic')
            ->assertDontSee('English')
            ->assertDontSee('French');
    }

    public function test_section_lead_counts_are_scoped_to_their_section(): void
    {
        $this->application(['preferred_section' => 'EN', 'status' => 'accepted']);
        $this->application(['preferred_section' => 'EN', 'status' => 'rejected']);
        $this->application(['preferred_section' => 'FR', 'status' => 'accepted']);
        $this->application(['preferred_section' => 'AR', 'status' => 'pending']);

        $this->actingAs($this->sectionLead('english'))
            ->get(route('applications.index'))
            ->assertOk()
            ->assertViewHas('counts', function (array $counts) {
                return $counts['all'] === 2
                    && $counts['accepted'] === 1
                    && $counts['rejected'] === 1
                    && $counts['pending'] === 0
                    && $counts['passed_to_test'] === 0;
            });
    }

    public function test_president_still_sees_every_section(): void
    {
        $this->application(['first_name' => 'English', 'preferred_section' => 'EN']);
        $this->application(['first_name' => 'French', 'preferred_section' => 'FR']);
        $this->application(['first_name' => 'Arabic', 'preferred_section' => 'AR']);

        $this->actingAs($this->manager('president'))
            ->get(route('applications.index'))
            ->assertOk()
            ->assertSee('English')
            ->assertSee('French')
            ->assertSee('Arabic');
    }

    public function test_president_can_filter_applications_by_section(): void
    {
        $this->application(['first_name' => 'EnOnly', 'preferred_section' => 'EN']);
        $this->application(['first_name' => 'FrOnly', 'preferred_section' => 'FR']);
        $this->application(['first_name' => 'ArOnly', 'preferred_section' => 'AR']);

        $manager = $this->manager('president');

        $this->actingAs($manager)
            ->get(route('applications.index', ['section' => 'EN']))
            ->assertOk()
            ->assertSee('EnOnly')
            ->assertDontSee('FrOnly')
            ->assertDontSee('ArOnly')
            ->assertViewHas('activeSection', 'EN');

        $this->actingAs($manager)
            ->get(route('applications.index', ['section' => 'FR']))
            ->assertOk()
            ->assertSee('FrOnly')
            ->assertDontSee('EnOnly')
            ->assertDontSee('ArOnly');

        $this->actingAs($manager)
            ->get(route('applications.index', ['section' => 'AR']))
            ->assertOk()
            ->assertSee('ArOnly')
            ->assertDontSee('EnOnly')
            ->assertDontSee('FrOnly');
    }

    public function test_section_filter_is_combined_with_status_and_counts(): void
    {
        $this->application(['first_name' => 'EnglishPending', 'preferred_section' => 'EN']);
        $this->application(['first_name' => 'EnglishAccepted', 'preferred_section' => 'EN', 'status' => 'accepted']);
        $this->application(['first_name' => 'FrenchAccepted', 'preferred_section' => 'FR', 'status' => 'accepted']);

        $this->actingAs($this->manager('president'))
            ->get(route('applications.index', ['section' => 'EN', 'status' => 'accepted']))
            ->assertOk()
            ->assertSee('EnglishAccepted')
            ->assertDontSee('EnglishPending')
            ->assertDontSee('FrenchAccepted')
            ->assertViewHas('counts', function (array $counts) {
                return $counts['all'] === 2
                    && $counts['accepted'] === 1
                    && $counts['pending'] === 1;
            });
    }

    public function test_invalid_section_filter_is_ignored(): void
    {
        $this->application(['first_name' => 'EnOnly', 'preferred_section' => 'EN']);
        $this->application(['first_name' => 'FrOnly', 'preferred_section' => 'FR']);

        $this->actingAs($this->manager('president'))
            ->get(route('applications.index', ['section' => 'XX']))
            ->assertOk()
            ->assertSee('EnOnly')
            ->assertSee('FrOnly')
            ->assertViewHas('activeSection', null);
    }

    public function test_section_lead_cannot_override_their_section_filter(): void
    {
        $this->application(['first_name' => 'EnOnly', 'preferred_section' => 'EN']);
        $this->application(['first_name' => 'FrOnly', 'preferred_section' => 'FR']);

        $this->actingAs($this->sectionLead('english'))
            ->get(route('applications.index', ['section' => 'FR']))
            ->assertOk()
            ->assertSee('EnOnly')
            ->assertDontSee('FrOnly')
            ->assertViewHas('activeSection', 'EN')
            ->assertViewHas('canFilterSections', false);
    }

    // --- PDF exports -------------------------------------------------------

    public function test_manager_can_export_all_applications_as_pdf(): void
    {
        $this->application(['first_name' => 'EnOnly', 'preferred_section' => 'EN']);
        $this->application(['first_name' => 'FrOnly', 'preferred_section' => 'FR']);

        $response = $this->actingAs($this->manager('president'))
            ->get(route('applications.export'));

        $response->assertOk()
            ->assertDownload()
            ->assertHeader('content-type', 'application/pdf');

        $content = $response->getContent();

        $this->assertStringStartsWith('%PDF', $content);
        $this->assertStringContainsString(
            'applications-all-',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_manager_can_export_a_single_section_as_pdf(): void
    {
        $this->application(['first_name' => 'EnOnly', 'preferred_section' => 'EN']);
        $this->application(['first_name' => 'FrOnly', 'preferred_section' => 'FR']);

        $response = $this->actingAs($this->manager('president'))
            ->get(route('applications.export', ['section' => 'EN']));

        $response->assertOk()
            ->assertDownload()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString(
            'applications-english-',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_invalid_section_export_falls_back_to_all_applications(): void
    {
        $this->application(['first_name' => 'EnOnly', 'preferred_section' => 'EN']);
        $this->application(['first_name' => 'FrOnly', 'preferred_section' => 'FR']);

        $response = $this->actingAs($this->manager('president'))
            ->get(route('applications.export', ['section' => 'XX']));

        $response->assertOk()
            ->assertDownload()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString(
            'applications-all-',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_section_lead_cannot_export_applications(): void
    {
        $this->application(['preferred_section' => 'EN']);

        $this->actingAs($this->sectionLead('english'))
            ->get(route('applications.export'))
            ->assertForbidden();
    }

    public function test_exported_phone_numbers_keep_their_leading_zero(): void
    {
        $application = $this->application([
            'first_name' => 'PhoneTester',
            'phone_number' => '0612345678',
        ]);

        $html = view('applications.export', [
            'applications' => collect([$application]),
            'total' => 1,
            'sectionLabel' => 'All sections',
            'generatedAt' => now()->format('M j, Y · H:i'),
        ])->render();

        $this->assertStringContainsString('0612345678', $html);
    }

    public function test_applications_page_shows_pdf_download_buttons_for_managers(): void
    {
        $this->actingAs($this->manager('president'))
            ->get(route('applications.index'))
            ->assertOk()
            ->assertSee('Download applications')
            ->assertSee('All applicants')
            ->assertSee('English applicants')
            ->assertSee('French applicants')
            ->assertSee('Arabic applicants')
            ->assertSee(route('applications.export'), false)
            ->assertSee(route('applications.export', ['section' => 'EN']), false);
    }

    public function test_section_leads_do_not_see_pdf_download_buttons(): void
    {
        $this->actingAs($this->sectionLead('english'))
            ->get(route('applications.index'))
            ->assertOk()
            ->assertDontSee('Download applications');
    }

    public function test_section_lead_cannot_view_another_sections_application(): void
    {
        $application = $this->application(['preferred_section' => 'FR']);

        $this->assertFalse(
            $this->sectionLead('english')->can('view', $application)
        );
    }

    public function test_section_lead_can_only_view_not_manage_applications(): void
    {
        $application = $this->application(['preferred_section' => 'EN']);

        $sectionLead = $this->sectionLead('english');

        $this->assertTrue($sectionLead->can('view', $application));
        $this->assertFalse($sectionLead->can('updateStatus', $application));
        $this->assertFalse($sectionLead->can('delete', $application));

        $this->actingAs($sectionLead)
            ->patchJson(route('applications.status', $application), ['status' => 'accepted'])
            ->assertForbidden();

        $this->assertSame('pending', $application->fresh()->status);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('applications.index'))->assertRedirect(route('login'));
    }

    // --- Filters + search --------------------------------------------------

    public function test_status_filter_returns_only_matching_applications(): void
    {
        $pending = $this->application(['first_name' => 'Pendy']);
        $accepted = $this->application(['first_name' => 'Accepy', 'status' => 'accepted']);
        $rejected = $this->application(['first_name' => 'Rejecty', 'status' => 'rejected']);
        $passed = $this->application(['first_name' => 'Passy', 'status' => 'passed_to_test']);

        $manager = $this->manager();

        $this->actingAs($manager)->get(route('applications.index', ['status' => 'pending']))
            ->assertOk()->assertSee('Pendy')->assertDontSee('Accepy');

        $this->actingAs($manager)->get(route('applications.index', ['status' => 'accepted']))
            ->assertOk()->assertSee('Accepy')->assertDontSee('Pendy');

        $this->actingAs($manager)->get(route('applications.index', ['status' => 'rejected']))
            ->assertOk()->assertSee('Rejecty')->assertDontSee('Accepy');

        $this->actingAs($manager)->get(route('applications.index', ['status' => 'passed_to_test']))
            ->assertOk()->assertSee('Passy')->assertDontSee('Rejecty');

        // "All" shows everyone.
        $this->actingAs($manager)->get(route('applications.index'))
            ->assertOk()->assertSee('Pendy')->assertSee('Accepy')->assertSee('Rejecty')->assertSee('Passy');
    }

    public function test_status_counts_are_exposed_to_the_view(): void
    {
        $this->application(['status' => 'accepted']);
        $this->application(['status' => 'accepted']);
        $this->application(['status' => 'rejected']);

        $response = $this->actingAs($this->manager())
            ->get(route('applications.index'));

        $response->assertOk()
            ->assertViewHas('counts', function (array $counts) {
                return $counts['all'] === 3
                    && $counts['pending'] === 0
                    && $counts['accepted'] === 2
                    && $counts['rejected'] === 1
                    && $counts['passed_to_test'] === 0;
            })
            // Assert statistics KPI strip is rendered
            ->assertSee('data-kpi="all"', false)
            ->assertSee('data-kpi="accepted"', false)
            ->assertSee('data-kpi="rejected"', false)
            ->assertSee('Total Applications')
            ->assertSee('Pending');
    }

    public function test_search_matches_name_email_cne_and_phone(): void
    {
        $byName = $this->application(['first_name' => 'Khadija', 'last_name' => 'Bennani']);
        $byEmail = $this->application(['email' => 'findme@example.com']);
        $byCne = $this->application(['cne' => 'ZXY98765']);
        $byPhone = $this->application(['phone_number' => '0699887766']);

        $manager = $this->manager();

        $this->actingAs($manager)->get(route('applications.index', ['q' => 'Khadija']))
            ->assertOk()->assertSee('Khadija Bennani')->assertDontSee('findme@example.com');

        $this->actingAs($manager)->get(route('applications.index', ['q' => 'findme@example.com']))
            ->assertOk()->assertSee('findme@example.com');

        $this->actingAs($manager)->get(route('applications.index', ['q' => 'ZXY98765']))
            ->assertOk()->assertSee('ZXY98765');

        $this->actingAs($manager)->get(route('applications.index', ['q' => '0699887766']))
            ->assertOk()->assertSee('0699887766');
    }

    public function test_search_and_pagination_work_together_and_preserve_query(): void
    {
        for ($i = 0; $i < 18; $i++) {
            $this->application(['last_name' => 'Matchable'.$i, 'email' => "match{$i}@example.com"]);
        }
        $this->application(['last_name' => 'Irrelevant']);

        $manager = $this->manager();

        $response = $this->actingAs($manager)->get(route('applications.index', ['q' => 'Matchable', 'status' => 'pending']));

        $response->assertOk();

        $response->assertViewHas('applications', function ($paginator) {
            return $paginator->total() === 18
                && $paginator->count() === 15
                && str_contains((string) $paginator->nextPageUrl(), 'q=Matchable')
                && str_contains((string) $paginator->nextPageUrl(), 'status=pending');
        });

        // Page 2 still filtered by the search term.
        $this->actingAs($manager)
            ->get(route('applications.index', ['q' => 'Matchable', 'status' => 'pending', 'page' => 2]))
            ->assertOk()
            ->assertViewHas('applications', fn ($paginator) => $paginator->count() === 3);
    }

    // --- Status changes ----------------------------------------------------

    public function test_accept_updates_the_database(): void
    {
        $application = $this->application();

        $this->actingAs($this->manager())
            ->patchJson(route('applications.status', $application), ['status' => 'accepted'])
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'accepted']);

        $this->assertSame('accepted', $application->fresh()->status);
    }

    public function test_reject_updates_the_database(): void
    {
        $application = $this->application();

        $this->actingAs($this->manager())
            ->patchJson(route('applications.status', $application), ['status' => 'rejected'])
            ->assertOk();

        $this->assertSame('rejected', $application->fresh()->status);
    }

    public function test_pass_to_test_updates_the_database(): void
    {
        $application = $this->application();

        $this->actingAs($this->manager())
            ->patchJson(route('applications.status', $application), ['status' => 'passed_to_test'])
            ->assertOk();

        $this->assertSame('passed_to_test', $application->fresh()->status);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $application = $this->application();

        $this->actingAs($this->manager())
            ->patch(route('applications.status', $application), ['status' => 'hacked'])
            ->assertSessionHasErrors('status');

        $this->assertSame('pending', $application->fresh()->status);
    }

    public function test_status_change_requires_patch_not_get(): void
    {
        $application = $this->application();

        $this->actingAs($this->manager())
            ->get(route('applications.status', $application))
            ->assertStatus(405);
    }

    // --- Delete ------------------------------------------------------------

    public function test_delete_removes_only_the_intended_application(): void
    {
        $keep = $this->application(['first_name' => 'Keep']);
        $remove = $this->application(['first_name' => 'Remove']);

        $this->actingAs($this->manager())
            ->deleteJson(route('applications.destroy', $remove))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseMissing('users_recrute', ['id' => $remove->id]);
        $this->assertDatabaseHas('users_recrute', ['id' => $keep->id]);
    }

    // --- Authorization on actions -----------------------------------------

    public function test_non_manager_cannot_change_status(): void
    {
        $application = $this->application();

        $this->actingAs($this->member())
            ->patch(route('applications.status', $application), ['status' => 'accepted'])
            ->assertForbidden();

        $this->assertSame('pending', $application->fresh()->status);
    }

    public function test_non_manager_cannot_delete(): void
    {
        $application = $this->application();

        $this->actingAs($this->member())
            ->delete(route('applications.destroy', $application))
            ->assertForbidden();

        $this->assertDatabaseHas('users_recrute', ['id' => $application->id]);
    }

    // --- Confirmation UI (no native dialogs, action metadata present) ------

    public function test_dashboard_renders_custom_confirmation_hooks_not_native_dialogs(): void
    {
        $application = $this->application(['first_name' => 'Modal', 'last_name' => 'Tester']);

        $response = $this->actingAs($this->manager())->get(route('applications.index'));

        $response->assertOk()
            // Manage card + reusable JS confirmation component hooks.
            ->assertSee('data-manage', false)
            ->assertSee('data-status-url=', false)
            ->assertSee('data-delete-url=', false)
            ->assertSee('TGD.menu', false)
            ->assertSee('TGD.confirm', false)
            ->assertSee('data-view', false)
            // The old clipped dropdown must be gone.
            ->assertDontSee('data-action=', false)
            // Never fall back to the browser's native dialogs.
            ->assertDontSee('window.confirm', false)
            ->assertDontSee('window.alert', false)
            ->assertDontSee('onclick="return confirm', false);
    }
}
