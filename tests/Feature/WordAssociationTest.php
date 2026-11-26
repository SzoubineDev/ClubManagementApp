<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordAssociationTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
            'is_suspended' => false,
        ]);
    }

    public function test_guests_cannot_reach_the_word_association_game(): void
    {
        $this->get(route('games.word-assoc'))->assertRedirect(route('login'));
        $this->post(route('games.word-assoc.draw'))->assertRedirect(route('login'));
        $this->post(route('games.word-assoc.reveal'))->assertRedirect(route('login'));
        $this->delete(route('games.word-assoc.reset'))->assertRedirect(route('login'));
    }

    public function test_the_word_association_page_loads(): void
    {
        $this->actingAs($this->user())
            ->get(route('games.word-assoc'))
            ->assertOk()
            ->assertSee('Word Association')
            ->assertSee('How to play')
            ->assertSee('The deck')
            ->assertSee('id="figureText"', false)
            ->assertSee('id="answerPanel"', false)
            ->assertSee('id="timerProgress"', false)
            ->assertSee('id="restartButton"', false)
            ->assertSee('Restart');
    }

    public function test_draws_return_unique_figures_and_a_sixty_second_round(): void
    {
        $this->actingAs($this->user());

        $seen = [];

        for ($i = 0; $i < 50; $i++) {
            $response = $this->postJson(route('games.word-assoc.draw'))
                ->assertOk()
                ->assertJsonStructure(['id', 'figure', 'hint', 'seconds', 'used', 'remaining']);

            $id = $response->json('id');

            $this->assertNotContains($id, $seen);
            $this->assertNotEmpty($response->json('figure'));
            $this->assertNotEmpty($response->json('hint'));
            $this->assertSame(60, $response->json('seconds'));
            $this->assertSame(50 - ($i + 1), $response->json('remaining'));

            $seen[] = $id;
        }

        $this->assertCount(50, $seen);
        $this->assertSame(50, count(array_unique($seen)));
    }

    public function test_a_full_deck_refuses_to_draw_again(): void
    {
        $this->actingAs($this->user());

        $all = range(1, 50);

        $this->withSession(['word_assoc.used' => $all])
            ->postJson(route('games.word-assoc.draw'))
            ->assertStatus(409)
            ->assertJson(['remaining' => 0]);
    }

    public function test_reveal_returns_the_answer_for_the_current_figure(): void
    {
        $this->actingAs($this->user());

        $this->postJson(route('games.word-assoc.reveal'))
            ->assertStatus(409);

        $drawn = $this->postJson(route('games.word-assoc.draw'))->assertOk();

        $revealed = $this->postJson(route('games.word-assoc.reveal'))
            ->assertOk()
            ->assertJsonStructure(['id', 'figure', 'answer', 'explanation'])
            ->assertJson([
                'id' => $drawn->json('id'),
                'figure' => $drawn->json('figure'),
            ]);

        $this->assertNotEmpty($revealed->json('answer'));
        $this->assertNotEmpty($revealed->json('explanation'));
    }

    public function test_the_seafood_example_is_part_of_the_deck(): void
    {
        $this->actingAs($this->user());

        $found = false;

        for ($i = 0; $i < 50; $i++) {
            $drawn = $this->postJson(route('games.word-assoc.draw'))->assertOk();
            $revealed = $this->postJson(route('games.word-assoc.reveal'))->assertOk();

            if ($revealed->json('answer') === 'Seafood') {
                $found = true;
                $this->assertSame('F👀D', $drawn->json('figure'));
            }
        }

        $this->assertTrue($found);
    }

    public function test_reset_returns_every_figure_to_the_deck(): void
    {
        $this->actingAs($this->user());

        $this->postJson(route('games.word-assoc.draw'))->assertOk();

        $this->deleteJson(route('games.word-assoc.reset'))
            ->assertOk()
            ->assertJson([
                'used' => [],
                'remaining' => 50,
            ]);

        $this->assertFalse(session()->has('word_assoc.used'));
        $this->assertFalse(session()->has('word_assoc.current'));
    }
}
