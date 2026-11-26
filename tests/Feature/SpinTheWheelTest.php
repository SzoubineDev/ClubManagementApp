<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpinTheWheelTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'is_suspended' => false,
        ]);
    }

    public function test_guests_cannot_reach_the_wheel(): void
    {
        $this->get(route('games.spin-the-wheel'))->assertRedirect(route('login'));
        $this->post(route('games.spin-the-wheel.spin'))->assertRedirect(route('login'));
    }

    public function test_the_wheel_page_loads(): void
    {
        $this->actingAs($this->user())
            ->get(route('games.spin-the-wheel'))
            ->assertOk()
            ->assertSee('Spin the Wheel')
            ->assertSee('1 to 50')
            ->assertSee('50 of 50 left')
            ->assertSee('Presenter board')
            ->assertSee('id="resultOverlay"', false)
            ->assertSee('id="resultImage"', false)
            ->assertSee('/images/memes/meme1.png');
    }

    public function test_spins_return_unique_numbers_between_one_and_fifty(): void
    {
        $this->actingAs($this->user());

        $seen = [];

        for ($i = 0; $i < 12; $i++) {
            $response = $this->postJson(route('games.spin-the-wheel.spin'))
                ->assertOk()
                ->assertJsonStructure(['number', 'drawn', 'remaining', 'announcement', 'image', 'spin_audio', 'result_audio']);

            $number = $response->json('number');
            $image = $response->json('image');

            $this->assertNotNull($image);
            $this->assertMatchesRegularExpression('#/images/memes/meme([1-9]|1\d|20)\.(png|jpe?g|webp|gif)$#i', $image);

            $this->assertGreaterThanOrEqual(1, $number);
            $this->assertLessThanOrEqual(50, $number);
            $this->assertNotContains($number, $seen);
            $this->assertSame(
                'Number '.$number.' is going to present themselves now.',
                $response->json('announcement')
            );

            $seen[] = $number;

            $this->assertSame(50 - ($i + 1), $response->json('remaining'));
            $this->assertCount($i + 1, $response->json('drawn'));
        }

        $this->assertSame(count($seen), count(array_unique($seen)));
    }

    public function test_reset_returns_every_number_to_the_wheel(): void
    {
        $this->actingAs($this->user());

        $this->postJson(route('games.spin-the-wheel.spin'))->assertOk();

        $this->deleteJson(route('games.spin-the-wheel.reset'))
            ->assertOk()
            ->assertJson([
                'drawn' => [],
                'remaining' => 50,
            ]);

        $this->assertNull(session('spin_the_wheel.last'));
        $this->assertFalse(session()->has('spin_the_wheel.drawn'));
    }

    public function test_a_full_board_refuses_to_draw_again(): void
    {
        $this->actingAs($this->user());

        $all = range(1, 50);

        $this->withSession([
            'spin_the_wheel.drawn' => $all,
            'spin_the_wheel.last' => 50,
        ])->postJson(route('games.spin-the-wheel.spin'))
            ->assertStatus(409)
            ->assertJson(['remaining' => 0]);
    }

    public function test_audios_play_in_shuffled_cycles_without_repeating_until_exhausted(): void
    {
        $tempPublic = sys_get_temp_dir().'/spin_wheel_audio_test_'.uniqid();
        $audioDir = $tempPublic.'/audios';
        mkdir($audioDir, 0777, true);

        for ($i = 1; $i <= 10; $i++) {
            file_put_contents($audioDir.'/spin'.$i.'.mp3', 'spin'.$i);
            file_put_contents($audioDir.'/result'.$i.'.mp3', 'result'.$i);
        }

        $originalPublicPath = $this->app->publicPath();
        $this->app->usePublicPath($tempPublic);

        try {
            $this->actingAs($this->user());

            $firstCycleSpin = [];
            $firstCycleResult = [];

            for ($i = 0; $i < 10; $i++) {
                $response = $this->postJson(route('games.spin-the-wheel.spin'))->assertOk();
                $firstCycleSpin[] = basename((string) $response->json('spin_audio'));
                $firstCycleResult[] = basename((string) $response->json('result_audio'));
            }

            $this->assertCount(10, array_unique($firstCycleSpin));
            $this->assertCount(10, array_unique($firstCycleResult));

            $secondCycleSpin = [];
            $secondCycleResult = [];

            for ($i = 0; $i < 10; $i++) {
                $response = $this->postJson(route('games.spin-the-wheel.spin'))->assertOk();
                $secondCycleSpin[] = basename((string) $response->json('spin_audio'));
                $secondCycleResult[] = basename((string) $response->json('result_audio'));
            }

            $this->assertCount(10, array_unique($secondCycleSpin));
            $this->assertCount(10, array_unique($secondCycleResult));
            $this->assertNotSame($firstCycleSpin[9], $secondCycleSpin[0]);
            $this->assertNotSame($firstCycleResult[9], $secondCycleResult[0]);
        } finally {
            $this->app->usePublicPath($originalPublicPath);

            foreach (glob($audioDir.'/*') ?: [] as $file) {
                @unlink($file);
            }
            @rmdir($audioDir);
            @rmdir($tempPublic);
        }
    }
}
