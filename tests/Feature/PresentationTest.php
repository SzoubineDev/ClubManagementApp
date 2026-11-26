<?php

namespace Tests\Feature;

use Tests\TestCase;

class PresentationTest extends TestCase
{
    public function test_the_presentation_is_public_and_loads(): void
    {
        $response = $this->get(route('presentation'))
            ->assertOk()
            ->assertSee("Journée")
            ->assertSee('Azul')
            ->assertSee('Hymne')
            ->assertSee('Chérifien')
            ->assertSee('Zoubine Soulaimane')
            ->assertSee('Wissal Jamal')
            ->assertSee('Baidi Oussama')
            ->assertSee('Hammali Imane')
            ->assertSee('Ghezlane Herraf')
            ->assertSee('Abdousse NourEddine')
            ->assertSee('Aymane Fassali')
            ->assertSee('Oubahou Fatima Ezahrae')
            ->assertSee('Games Time')
            ->assertSee('We love you')
            ->assertSee('images/team/zoubine-soulaimane.jpg');

        $this->assertSame(13, substr_count($response->getContent(), 'class="slide '));
    }

    public function test_the_assistants_are_not_in_the_presentation(): void
    {
        $this->get(route('presentation'))
            ->assertOk()
            ->assertDontSee('Fanta Ba')
            ->assertDontSee('Khaoula Meggouri')
            ->assertDontSee('Event Manager Assistant')
            ->assertDontSee('General assistant');
    }
}
