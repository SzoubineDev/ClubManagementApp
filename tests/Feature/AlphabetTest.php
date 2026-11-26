<?php

namespace Tests\Feature;

use Tests\TestCase;

class AlphabetTest extends TestCase
{
    public function test_the_alphabets_page_is_public_and_shows_a_to_z(): void
    {
        $response = $this->get(route('alphabets'))
            ->assertOk()
            ->assertSee('The <em>Alphabet</em>', false)
            ->assertSee('26 letters')
            ->assertSee('data-letter="A"', false)
            ->assertSee('data-letter="M"', false)
            ->assertSee('data-letter="Z"', false)
            ->assertSee('images/logo.jpg');

        $this->assertSame(26, substr_count($response->getContent(), 'class="letter"'));
    }
}
