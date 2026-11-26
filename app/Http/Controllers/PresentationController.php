<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\View\View;

class PresentationController extends Controller
{
    /**
     * Bureau members shown during the integration-day presentation.
     * The General Assistant and Event Manager Assistant are intentionally
     * left out.
     *
     * @return array<int, array{name:string, role:string, dept:string, line:string}>
     */
    private function members(): array
    {
        return [
            [
                'name' => 'Zoubine Soulaimane',
                'role' => 'President',
                'dept' => 'Executive Office',
                'line' => 'Leading the club, setting the vision, and keeping every voice in the room.',
            ],
            [
                'name' => 'Wissal Jamal',
                'role' => 'Vice President',
                'dept' => 'Coordination',
                'line' => 'Coordinating the bureau and making sure every idea becomes a real project.',
            ],
            [
                'name' => 'Baidi Oussama',
                'role' => 'RH Manager',
                'dept' => 'Public Relations',
                'line' => 'Recruiting, supporting, and taking care of every member.',
            ],
            [
                'name' => 'Hammali Imane',
                'role' => 'Media Manager',
                'dept' => 'Brand & Creative',
                'line' => "Telling the club's story across every platform.",
            ],
            [
                'name' => 'Ghezlane Herraf',
                'role' => 'Design Manager',
                'dept' => 'Public Relations',
                'line' => 'Shaping the visual identity of The Great Debaters.',
            ],
            [
                'name' => 'Abdousse NourEddine',
                'role' => 'Event Manager',
                'dept' => 'French Eloquence',
                'line' => 'Planning the debates, workshops, and moments that bring us together.',
            ],
            [
                'name' => 'Aymane Fassali',
                'role' => 'English Section Lead',
                'dept' => 'English BP Track',
                'line' => 'Coaching English debate and building confident speakers.',
            ],
            [
                'name' => 'Oubahou Fatima Ezahrae',
                'role' => 'Arabic Section Lead',
                'dept' => 'Arabic Rhetoric',
                'line' => 'Leading Arabic rhetoric and the art of persuasion.',
            ],
        ];
    }

    public function index(): View
    {
        $members = array_map(function (array $member): array {
            $slug = Str::slug($member['name']);

            $member['slug'] = $slug;
            $member['photo'] = 'images/team/'.$slug.'.jpg';
            $member['initials'] = collect(explode(' ', $member['name']))
                ->filter()
                ->take(2)
                ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
                ->implode('');

            return $member;
        }, $this->members());

        // Intro + anthem + member slides + introduce-yourself + games + outro.
        $totalSlides = 2 + count($members) + 3;

        return view('presentation', [
            'members' => $members,
            'totalSlides' => $totalSlides,
        ]);
    }
}
