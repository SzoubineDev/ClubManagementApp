<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EventWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Register the roles used by the app's role-based policies.
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
        ]);
        $u->assignRole('member');

        return $u;
    }

    public function test_president_can_create_event_page(): void
    {
        $this->actingAs($this->president())
            ->get(route('events.create'))
            ->assertOk();
    }

    public function test_member_cannot_access_create_or_store(): void
    {
        $this->actingAs($this->member())
            ->get(route('events.create'))
            ->assertForbidden();

        $this->actingAs($this->member())
            ->post(route('events.store'), [
                'title' => 'Hacked event',
                'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('events', ['title' => 'Hacked event']);
    }

    public function test_president_creates_draft_by_default_and_it_is_hidden_from_members(): void
    {
        $president = $this->president();

        $response = $this->actingAs($president)->post(route('events.store'), [
            'title' => 'Draft debate night',
            'description' => 'Workshop',
            'location' => 'Amphi A',
            'starts_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'submit_action' => 'draft',
        ]);

        $event = Event::firstWhere('title', 'Draft debate night');
        $this->assertNotNull($event);
        $this->assertSame('draft', $event->status);
        $this->assertNull($event->published_at);

        // Members cannot see or open the draft.
        $member = $this->member();
        $index = $this->actingAs($member)->get(route('events.index'))->getContent();
        $this->assertStringNotContainsString('Draft debate night', $index);

        $this->actingAs($member)->get(route('events.show', $event))->assertForbidden();

        // The president can.
        $this->actingAs($president)->get(route('events.show', $event))->assertOk();
    }

    public function test_draft_event_page_disables_rsvp_and_explains_why(): void
    {
        $president = $this->president();

        $event = Event::create([
            'title' => 'Unpublished workshop',
            'starts_at' => now()->addDays(3),
            'created_by' => $president->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($president)->get(route('events.show', $event));

        $response->assertOk();
        // The button is disabled and both the hover tooltip and the visible
        // helper text explain that the event is not published yet.
        $response->assertSee('disabled', false);
        $response->assertSee('This event is not published yet.', false);
        $response->assertSee('This event is still a draft. RSVP opens once it is published.');

        // No active RSVP form is rendered for a draft.
        $response->assertDontSee(route('events.rsvp', $event), false);

        // The server keeps rejecting direct RSVP attempts for drafts.
        $this->actingAs($president)
            ->post(route('events.rsvp', $event))
            ->assertForbidden();

        $this->assertDatabaseMissing('event_user', [
            'event_id' => $event->id,
            'user_id' => $president->id,
        ]);
    }

    public function test_published_event_page_keeps_the_active_rsvp_form(): void
    {
        $president = $this->president();

        $event = Event::create([
            'title' => 'Published workshop',
            'starts_at' => now()->addDays(3),
            'created_by' => $president->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->member())->get(route('events.show', $event));

        $response->assertOk();
        $response->assertSee(route('events.rsvp', $event), false);
        $response->assertDontSee('This event is still a draft. RSVP opens once it is published.');
    }

    public function test_president_can_publish_immediately_on_creation(): void
    {
        $response = $this->actingAs($this->president())->post(route('events.store'), [
            'title' => 'Published at creation',
            'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'submit_action' => 'publish',
        ]);

        if (! $response->isRedirect()) {
            $response->assertSessionHasNoErrors();
            $this->fail('Store did not redirect');
        }

        $event = Event::firstWhere('title', 'Published at creation');
        $this->assertSame('published', $event->status);
        $this->assertNotNull($event->published_at);

        $index = $this->actingAs($this->member())->get(route('events.index'))->getContent();
        $this->assertStringContainsString('Published at creation', $index);
    }

    public function test_president_can_publish_a_draft_later(): void
    {
        $president = $this->president();
        $event = Event::create([
            'title' => 'Later publish',
            'starts_at' => now()->addDays(4),
            'created_by' => $president->id,
            'status' => 'draft',
        ]);

        $this->actingAs($president)
            ->patch(route('events.publish', $event))
            ->assertRedirect();

        $event->refresh();
        $this->assertSame('published', $event->status);
        $this->assertNotNull($event->published_at);
    }

    public function test_member_cannot_publish_or_unpublish(): void
    {
        $president = $this->president();
        $event = Event::create([
            'title' => 'Guarded draft',
            'starts_at' => now()->addDays(4),
            'created_by' => $president->id,
            'status' => 'draft',
        ]);

        $member = $this->member();

        $this->actingAs($member)->patch(route('events.publish', $event))->assertForbidden();
        $this->actingAs($member)->patch(route('events.unpublish', $event))->assertForbidden();

        $this->assertSame('draft', $event->fresh()->status);
    }

    public function test_unpublish_returns_event_to_draft(): void
    {
        $president = $this->president();
        $event = Event::create([
            'title' => 'Revert me',
            'starts_at' => now()->addDays(2),
            'created_by' => $president->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($president)->patch(route('events.unpublish', $event));

        $event->refresh();
        $this->assertSame('draft', $event->status);
        $this->assertNull($event->published_at);

        $index = $this->actingAs($this->member())->get(route('events.index'))->getContent();
        $this->assertStringNotContainsString('Revert me', $index);
    }

    public function test_create_event_page_renders_form_fields(): void
    {
        $response = $this->actingAs($this->president())
            ->get(route('events.create'));

        $response->assertOk();
        $response->assertSee('Event Title');
        $response->assertSee('Location / Venue');
        $response->assertSee('Starts at');
        $response->assertSee('Ends at');
        $response->assertSee('Capacity');
        $response->assertSee('Audience Restriction');
        $response->assertSee('Open to All');
        $response->assertSee('Restrict to Team');
        $response->assertSee('Restrict to Filière', false);
    }

    public function test_president_can_create_event_with_team_targeting(): void
    {
        $response = $this->actingAs($this->president())->post(route('events.store'), [
            'title' => 'Arabic Debate Workshop',
            'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'team' => 'arabic',
            'submit_action' => 'publish',
        ]);

        $response->assertRedirect();
        $event = Event::firstWhere('title', 'Arabic Debate Workshop');
        $this->assertNotNull($event);
        $this->assertSame('arabic', $event->team);
        $this->assertNull($event->filiere);
    }

    public function test_president_can_create_event_with_filiere_targeting(): void
    {
        $response = $this->actingAs($this->president())->post(route('events.store'), [
            'title' => 'Master Student Research Talk',
            'starts_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'filiere' => 'Master',
            'submit_action' => 'draft',
        ]);

        $response->assertRedirect();
        $event = Event::firstWhere('title', 'Master Student Research Talk');
        $this->assertNotNull($event);
        $this->assertSame('Master', $event->filiere);
        $this->assertNull($event->team);
    }

    public function test_president_can_create_event_with_capacity(): void
    {
        $response = $this->actingAs($this->president())->post(route('events.store'), [
            'title' => 'Exclusive Round Table',
            'starts_at' => now()->addDays(4)->format('Y-m-d H:i:s'),
            'capacity' => 30,
            'submit_action' => 'publish',
        ]);

        $response->assertRedirect();
        $event = Event::firstWhere('title', 'Exclusive Round Table');
        $this->assertNotNull($event);
        $this->assertSame(30, $event->capacity);
    }

    public function test_event_form_rejects_conflicting_team_and_filiere(): void
    {
        $response = $this->actingAs($this->president())->post(route('events.store'), [
            'title' => 'Invalid Event',
            'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'team' => 'french',
            'filiere' => 'LICENSE',
        ]);

        $response->assertSessionHasErrors('filiere');
    }
}
