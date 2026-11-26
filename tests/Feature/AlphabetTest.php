<?php

namespace Tests\Feature;

use Tests\TestCase;

class AlphabetTest extends TestCase
{
    public function test_the_alphabets_page_shows_both_alphabets_with_a_switch(): void
    {
        $response = $this->get(route('alphabets'))
            ->assertOk()
            ->assertSee('data-alphabet-target="en"', false)
            ->assertSee('data-alphabet-target="ar"', false)
            ->assertSee('The <em>Alphabet</em>', false)
            ->assertSee('الأبجدية <em>العربية</em>', false)
            ->assertSee('data-grid="en" dir="ltr"', false)
            ->assertSee('data-grid="ar" dir="rtl"', false)
            ->assertSee('data-letter="A"', false)
            ->assertSee('data-letter="Z"', false)
            ->assertSee('data-letter="ا"', false)
            ->assertSee('data-letter="ي"', false)
            ->assertSee('data-speak="باء"', false)
            ->assertSee('images/logo.jpg');

        // 26 English letters + 28 Arabic letters.
        $this->assertSame(54, substr_count($response->getContent(), 'class="letter"'));
    }
}
