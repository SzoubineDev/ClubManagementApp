<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRecrute;
use App\Models\Video;
use Database\Seeders\DesignVideoSeeder;
use Database\Seeders\VideoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClubRolesAndFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The migration creates the new management roles; base roles still
        // need to exist for role assignment in these tests.
        foreach (['member', 'president', 'admin', 'vice_president', 'secretary_general'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    private function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'is_suspended' => false,
            'email_verified_at' => now(),
        ], $attributes));

        $user->assignRole($role);

        return $user;
    }

    // --- Roles ------------------------------------------------------------

    public function test_requested_management_roles_exist(): void
    {
        foreach (['section_chef', 'rh_manager', 'media_manager', 'design_manager', 'assistant_general'] as $role) {
            $this->assertTrue(
                Role::where('name', $role)->where('guard_name', 'web')->exists(),
                "Role [{$role}] should exist."
            );
        }
    }

    // --- President assignment --------------------------------------------

    public function test_president_role_can_be_assigned_from_member_management(): void
    {
        $president = $this->userWithRole('president');
        $target = $this->userWithRole('member', ['name' => 'Future President']);

        $this->actingAs($president)
            ->put(route('members.update', $target), ['roles' => ['president']])
            ->assertRedirect(route('members.show', $target));

        $this->assertTrue($target->fresh()->hasRole('president'));

        // The previous president is demoted, never left with two presidents.
        $president->refresh();
        $this->assertFalse($president->hasRole('president'));
        $this->assertTrue($president->hasRole('member'));
    }

    // --- Section chef assignment -----------------------------------------

    public function test_section_chef_must_have_a_section(): void
    {
        $president = $this->userWithRole('president');
        $target = $this->userWithRole('member');

        $this->actingAs($president)
            ->from(route('members.edit', $target))
            ->put(route('members.update', $target), [
                'roles' => ['section_chef'],
                'team' => '',
            ])
            ->assertSessionHasErrors('team');

        $this->assertFalse($target->fresh()->hasRole('section_chef'));
    }

    public function test_section_chef_can_be_assigned_a_section(): void
    {
        $president = $this->userWithRole('president');
        $target = $this->userWithRole('member');

        $this->actingAs($president)
            ->put(route('members.update', $target), [
                'roles' => ['section_chef'],
                'team' => 'english',
            ])
            ->assertRedirect(route('members.show', $target));

        $target->refresh();

        $this->assertTrue($target->hasRole('section_chef'));
        $this->assertSame('english', $target->team);
        $this->assertSame('EN', $target->recruitmentSection());
    }

    // --- RH manager -------------------------------------------------------

    public function test_rh_manager_can_view_members_but_cannot_manage_them(): void
    {
        $rh = $this->userWithRole('rh_manager');
        $member = $this->userWithRole('member', ['name' => 'Visible Member']);

        $this->actingAs($rh)
            ->get(route('members.index'))
            ->assertOk()
            ->assertSee('Visible Member');

        $this->actingAs($rh)
            ->get(route('members.show', $member))
            ->assertOk()
            ->assertSee('Daily presence history');

        $this->actingAs($rh)
            ->get(route('members.edit', $member))
            ->assertForbidden();

        $this->assertFalse(Gate::forUser($rh)->allows('manage', $member));
        $this->assertFalse(Gate::forUser($rh)->allows('suspend', $member));
        $this->assertFalse(Gate::forUser($rh)->allows('delete', $member));
    }

    public function test_section_chef_only_sees_recruitment_applications_from_their_section(): void
    {
        $chef = $this->userWithRole('section_chef', ['team' => 'french']);

        $this->makeApplication(['first_name' => 'English Candidate', 'preferred_section' => 'EN']);
        $this->makeApplication(['first_name' => 'French Candidate', 'preferred_section' => 'FR']);
        $this->makeApplication(['first_name' => 'Arabic Candidate', 'preferred_section' => 'AR']);

        $this->actingAs($chef)
            ->get(route('applications.index'))
            ->assertOk()
            ->assertSee('French Candidate')
            ->assertDontSee('English Candidate')
            ->assertDontSee('Arabic Candidate');
    }

    private function makeApplication(array $attributes = []): UserRecrute
    {
        static $n = 0;
        $n++;

        return UserRecrute::create(array_merge([
            'first_name' => 'Candidate',
            'last_name' => 'Number'.$n,
            'phone_number' => '06000000'.str_pad((string) $n, 2, '0', STR_PAD_LEFT),
            'email' => "candidate{$n}@example.com",
            'cne' => 'CNE'.str_pad((string) $n, 5, '0', STR_PAD_LEFT),
            'filiere' => 'Cycle Ingénieur',
            'preferred_section' => 'EN',
            'motivation' => 'I want to debate.',
        ], $attributes));
    }

    public function test_role_specific_navigation_and_dashboard_actions_render(): void
    {
        $chef = $this->userWithRole('section_chef', ['team' => 'english']);

        $this->actingAs($chef)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My Section')
            ->assertSee('My Section Attendance');

        $media = $this->userWithRole('media_manager');

        $this->actingAs($media)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Media')
            ->assertSee('Media Library');

        $design = $this->userWithRole('design_manager');

        $this->actingAs($design)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Design')
            ->assertSee('Design Library');

        $assistant = $this->userWithRole('assistant_general');

        $this->actingAs($assistant)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Documents');

        $rh = $this->userWithRole('rh_manager');

        $this->actingAs($rh)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Member Directory');
    }

    // --- Section chef daily attendance -----------------------------------

    public function test_section_chef_sees_only_their_section_and_records_presence(): void
    {
        $chef = $this->userWithRole('section_chef', ['team' => 'english']);

        $englishOne = $this->userWithRole('member', ['name' => 'English One', 'team' => 'english']);
        $englishTwo = $this->userWithRole('member', ['name' => 'English Two', 'team' => 'english']);
        $frenchOne = $this->userWithRole('member', ['name' => 'French One', 'team' => 'french']);

        $this->actingAs($chef)
            ->get(route('section.attendance.index'))
            ->assertOk()
            ->assertSee('English One')
            ->assertSee('English Two')
            ->assertDontSee('French One');

        $today = today()->toDateString();

        $this->actingAs($chef)
            ->post(route('section.attendance.store'), [
                'date' => $today,
                'present' => [$englishOne->id],
            ])
            ->assertRedirect(route('section.attendance.index', ['date' => $today]));

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $englishOne->id,
            'date' => $today,
            'present' => true,
        ]);

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $englishTwo->id,
            'date' => $today,
            'present' => false,
        ]);

        $this->assertDatabaseMissing('attendance_records', [
            'user_id' => $frenchOne->id,
            'date' => $today,
        ]);
    }

    public function test_section_chef_without_a_section_is_forbidden(): void
    {
        $chef = $this->userWithRole('section_chef');

        $this->actingAs($chef)
            ->get(route('section.attendance.index'))
            ->assertForbidden();
    }

    // --- Media manager ----------------------------------------------------

    public function test_media_manager_can_view_the_media_library(): void
    {
        $media = $this->userWithRole('media_manager');
        Video::create([
            'title' => 'Club Debate Final',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'library' => Video::LIBRARY_MEDIA,
        ]);

        $this->actingAs($media)
            ->get(route('videos.index'))
            ->assertOk()
            ->assertSee('Club Debate Final');
    }

    public function test_media_library_is_read_only(): void
    {
        $media = $this->userWithRole('media_manager');
        Video::create([
            'title' => 'Library video',
            'youtube_url' => 'https://www.youtube.com/watch?v=iCvmsMzlF7o',
            'library' => Video::LIBRARY_MEDIA,
        ]);

        $response = $this->actingAs($media)->get(route('videos.index'));

        $response->assertOk();
        $response->assertDontSee('Add video');
        $response->assertDontSee('Remove');
        $response->assertDontSee('>Edit<', false);

        foreach (['videos.create', 'videos.store', 'videos.edit', 'videos.update', 'videos.destroy'] as $route) {
            $this->assertFalse(Route::has($route), "Route [{$route}] should not exist.");
        }
    }

    public function test_ordinary_member_cannot_access_the_media_library(): void
    {
        $member = $this->userWithRole('member');

        $this->actingAs($member)
            ->get(route('videos.index'))
            ->assertForbidden();
    }

    public function test_only_the_media_manager_role_can_access_the_media_library(): void
    {
        foreach (['president', 'vice_president', 'admin', 'rh_manager', 'section_chef', 'design_manager'] as $role) {
            $user = $this->userWithRole($role);
            $user->team = 'english';

            $this->actingAs($user)
                ->get(route('videos.index'))
                ->assertForbidden();
        }
    }

    public function test_media_library_shows_ten_random_videos_per_request(): void
    {
        $media = $this->userWithRole('media_manager');

        foreach (range(1, 25) as $i) {
            Video::create([
                'title' => "Sample video {$i}",
                'youtube_url' => 'https://www.youtube.com/watch?v=iCvmsMzlF7o',
                'sort_order' => $i,
            ]);
        }

        $response = $this->actingAs($media)->get(route('videos.index'));

        $response->assertOk();
        $response->assertSee('10 random picks from 25 videos');
        $this->assertSame(10, substr_count($response->getContent(), 'data-video-card'));
    }

    public function test_video_seeder_provides_one_hundred_videos(): void
    {
        $this->seed(VideoSeeder::class);

        $this->assertSame(100, Video::count());
    }

    // --- Design manager video-editing library -----------------------------

    public function test_design_manager_can_view_the_design_library(): void
    {
        $design = $this->userWithRole('design_manager');
        Video::create([
            'title' => 'CapCut Speed Ramp Tutorial',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'library' => Video::LIBRARY_DESIGN,
        ]);

        $this->actingAs($design)
            ->get(route('design.videos.index'))
            ->assertOk()
            ->assertSee('CapCut Speed Ramp Tutorial');
    }

    public function test_design_library_is_read_only(): void
    {
        $design = $this->userWithRole('design_manager');
        Video::create([
            'title' => 'Editing tutorial',
            'youtube_url' => 'https://www.youtube.com/watch?v=iCvmsMzlF7o',
            'library' => Video::LIBRARY_DESIGN,
        ]);

        $response = $this->actingAs($design)->get(route('design.videos.index'));

        $response->assertOk();
        $response->assertDontSee('Add tutorial');
        $response->assertDontSee('Remove');
        $response->assertDontSee('>Edit<', false);

        foreach (['design.videos.create', 'design.videos.store', 'design.videos.edit', 'design.videos.update', 'design.videos.destroy'] as $route) {
            $this->assertFalse(Route::has($route), "Route [{$route}] should not exist.");
        }
    }

    public function test_design_library_shows_ten_random_tutorials_per_request(): void
    {
        $design = $this->userWithRole('design_manager');

        foreach (range(1, 25) as $i) {
            Video::create([
                'title' => "Editing tutorial {$i}",
                'youtube_url' => 'https://www.youtube.com/watch?v=iCvmsMzlF7o',
                'sort_order' => $i,
                'library' => Video::LIBRARY_DESIGN,
            ]);
        }

        $response = $this->actingAs($design)->get(route('design.videos.index'));

        $response->assertOk();
        $response->assertSee('10 random video-editing tutorials from 25');
        $this->assertSame(10, substr_count($response->getContent(), 'data-video-card'));
    }

    public function test_design_video_seeder_provides_one_hundred_tutorials(): void
    {
        $this->seed(DesignVideoSeeder::class);

        $this->assertSame(
            100,
            Video::query()->inLibrary(Video::LIBRARY_DESIGN)->count()
        );
    }

    public function test_media_and_design_libraries_are_isolated(): void
    {
        $media = $this->userWithRole('media_manager');
        $design = $this->userWithRole('design_manager');

        Video::create([
            'title' => 'Media only video',
            'youtube_url' => 'https://www.youtube.com/watch?v=iCvmsMzlF7o',
            'library' => Video::LIBRARY_MEDIA,
        ]);

        Video::create([
            'title' => 'Design only tutorial',
            'youtube_url' => 'https://www.youtube.com/watch?v=Ks-_Mh1QhMc',
            'library' => Video::LIBRARY_DESIGN,
        ]);

        $this->actingAs($media)
            ->get(route('videos.index'))
            ->assertOk()
            ->assertSee('Media only video')
            ->assertDontSee('Design only tutorial');

        $this->actingAs($design)
            ->get(route('design.videos.index'))
            ->assertOk()
            ->assertSee('Design only tutorial')
            ->assertDontSee('Media only video');

        $this->actingAs($media)
            ->get(route('design.videos.index'))
            ->assertForbidden();

        $this->actingAs($design)
            ->get(route('videos.index'))
            ->assertForbidden();
    }

    public function test_video_thumbnail_is_derived_from_the_youtube_id(): void
    {
        $video = Video::create([
            'title' => 'Sample',
            'youtube_url' => 'https://youtu.be/iCvmsMzlF7o',
            'sort_order' => 1,
        ]);

        $this->assertSame('iCvmsMzlF7o', $video->youtubeId());
        $this->assertSame('https://img.youtube.com/vi/iCvmsMzlF7o/hqdefault.jpg', $video->thumbnail());
    }

    // --- Assistant general documents -------------------------------------

    public function test_assistant_general_can_download_the_room_request_document(): void
    {
        $assistant = $this->userWithRole('assistant_general');

        $this->actingAs($assistant)
            ->get(route('documents.index'))
            ->assertOk();

        $response = $this->actingAs($assistant)
            ->get(route('documents.room-request', [
                'room' => 'Amphithéâtre B',
                'date' => today()->toDateString(),
                'time' => '14:00 – 17:00',
                'purpose' => 'Weekly debate training',
            ]));

        $response->assertOk();
        $this->assertStringContainsString('attachment; filename="demande-de-salle.doc"', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('application/msword', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Demande de salle', $response->getContent());
    }

    public function test_ordinary_member_cannot_download_official_documents(): void
    {
        $member = $this->userWithRole('member');

        $this->actingAs($member)
            ->get(route('documents.index'))
            ->assertForbidden();

        $this->actingAs($member)
            ->get(route('documents.room-request'))
            ->assertForbidden();
    }
}
