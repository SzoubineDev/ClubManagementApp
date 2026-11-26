<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AlphabetController extends Controller
{
    /**
     * Public A–Z reference page, styled like the integration-day deck.
     */
    public function index(): View
    {
        $letters = range('A', 'Z');

        return view('alphabets', [
            'letters' => $letters,
            'totalLetters' => count($letters),
        ]);
    }
}
