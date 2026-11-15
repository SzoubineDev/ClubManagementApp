<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Daily presence register for a section chef.
 *
 * A section chef can only ever see and record members of their own section
 * (`users.team`); the section is derived from the authenticated user, never
 * from a request parameter.
 */
class SectionAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $chef = $request->user();
        $team = $this->authorizeChef($chef);

        $selectedDate = $this->selectedDate($request);

        $members = User::query()
            ->where('team', $team)
            ->orderBy('name')
            ->get();

        $memberIds = $members->pluck('id');

        $records = AttendanceRecord::query()
            ->whereIn('user_id', $memberIds)
            ->whereDate('date', $selectedDate->toDateString())
            ->get()
            ->keyBy('user_id');

        $history = AttendanceRecord::query()
            ->whereIn('user_id', $memberIds)
            ->whereDate('date', '>=', today()->subDays(13)->toDateString())
            ->selectRaw('date, SUM(present) as present_count, COUNT(*) as recorded_count')
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();

        return view('section-attendance.index', [
            'team' => $team,
            'sectionName' => $chef->sectionName(),
            'members' => $members,
            'records' => $records,
            'history' => $history,
            'selectedDate' => $selectedDate,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $chef = $request->user();
        $team = $this->authorizeChef($chef);

        $validated = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'present' => ['nullable', 'array'],
            'present.*' => ['integer'],
        ]);

        $date = Carbon::parse($validated['date'])->toDateString();

        $members = User::query()
            ->where('team', $team)
            ->pluck('id');

        $presentIds = collect($validated['present'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->intersect($members)
            ->flip();

        foreach ($members as $memberId) {
            AttendanceRecord::updateOrCreate(
                ['user_id' => $memberId, 'date' => $date],
                [
                    'present' => $presentIds->has($memberId),
                    'recorded_by' => $chef->id,
                ]
            );
        }

        return redirect()
            ->route('section.attendance.index', ['date' => $date])
            ->with('status', 'Presence saved for ' . Carbon::parse($date)->format('M j, Y') . '.');
    }

    /**
     * Only a section chef with an assigned section may use this register.
     */
    private function authorizeChef(User $user): string
    {
        $team = $user->sectionTeam();

        abort_unless($team !== null, 403, 'You are not assigned to a section.');

        return $team;
    }

    private function selectedDate(Request $request): Carbon
    {
        if (! $request->filled('date')) {
            return today();
        }

        try {
            $date = Carbon::parse($request->input('date'));
        } catch (\Throwable $e) {
            return today();
        }

        return $date->isFuture() ? today() : $date->startOfDay();
    }
}
