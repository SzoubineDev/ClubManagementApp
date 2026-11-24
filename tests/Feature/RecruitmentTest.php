<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRecrute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RecruitmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['member', 'president', 'vice_president', 'secretary_general', 'admin'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    public function test_recruitment_page_can_be_rendered(): void
    {
        $response = $this->get(route('recrute'));

        $response->assertOk();
        $response->assertSee('The Great Debaters');
        $response->assertSee('Recruitment');
    }

    public function test_late_recruitment_page_can_be_rendered(): void
    {
        $response = $this->get(route('late_recrute'));

        $response->assertOk();
        $response->assertSee('Late application form');
        $response->assertSee('Late recruitment');
    }

    public function test_late_application_is_flagged_and_can_be_filtered(): void
    {
        $payload = [
            'first_name' => 'Late',
            'last_name' => 'Applicant',
            'phone_number' => '0612345678',
            'email' => 'late.applicant@example.com',
            'cne' => 'LATE001',
            'filiere' => 'Génie Informatique',
            'preferred_section' => 'EN',
            'motivation' => 'I missed the regular deadline but would still like to join the club.',
        ];

        $response = $this->post(route('late_recrute.store'), $payload);

        $response->assertRedirect(route('late_recrute'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users_recrute', [
            'cne' => 'LATE001',
            'is_late' => true,
        ]);

        // An application from the regular season is not flagged as late.
        UserRecrute::create([
            'first_name' => 'Regular',
            'last_name' => 'Applicant',
            'phone_number' => '0600000000',
            'email' => 'regular.applicant@example.com',
            'cne' => 'REGULAR001',
            'filiere' => 'CPGE',
            'preferred_section' => 'FR',
            'motivation' => 'I applied during the regular recruitment season.',
        ]);

        $president = User::factory()->create(['is_suspended' => false]);
        $president->assignRole('president');

        $lateOnly = $this->actingAs($president)->get(route('applications.index', ['late' => 1]));
        $lateOnly->assertOk();
        $lateOnly->assertSee('Late Applicant');
        $lateOnly->assertDontSee('Regular Applicant');

        $all = $this->actingAs($president)->get(route('applications.index'));
        $all->assertOk();
        $all->assertSee('Late Applicant');
        $all->assertSee('Regular Applicant');
    }

    public function test_candidate_can_submit_recruitment_application(): void
    {
        $payload = [
            'first_name' => 'Amine',
            'last_name' => 'Tazi',
            'phone_number' => '0612345678',
            'email' => 'amine.tazi@example.com',
            'cne' => 'M134567890',
            'filiere' => 'Génie Informatique',
            'preferred_section' => 'FR',
            'motivation' => 'I want to develop my public speaking and debating skills in French.',
        ];

        $response = $this->post(route('recrute.store'), $payload);

        $response->assertRedirect(route('recrute'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users_recrute', [
            'first_name' => 'Amine',
            'last_name' => 'Tazi',
            'email' => 'amine.tazi@example.com',
            'cne' => 'M134567890',
            'status' => 'pending',
        ]);

        // President can view the submitted application in /applications
        $president = User::factory()->create(['is_suspended' => false]);
        $president->assignRole('president');

        $dashResponse = $this->actingAs($president)->get(route('applications.index'));
        $dashResponse->assertOk();
        $dashResponse->assertSee('Amine Tazi');
        $dashResponse->assertSee('M134567890');
        $dashResponse->assertSee('Pending');
    }

    public function test_duplicate_cne_is_rejected(): void
    {
        UserRecrute::create([
            'first_name' => 'Existing',
            'last_name' => 'Candidate',
            'phone_number' => '0600000000',
            'email' => 'existing@example.com',
            'cne' => 'DUPLICATE123',
            'filiere' => 'CPGE',
            'preferred_section' => 'EN',
            'motivation' => 'First submission.',
        ]);

        $payload = [
            'first_name' => 'Another',
            'last_name' => 'Person',
            'phone_number' => '0611111111',
            'email' => 'another@example.com',
            'cne' => 'duplicate123', // case variation
            'filiere' => 'ENSAM',
            'preferred_section' => 'EN',
            'motivation' => 'Second attempt with same CNE.',
        ];

        $response = $this->post(route('recrute.store'), $payload);

        $response->assertSessionHasErrors('cne');
    }
}
