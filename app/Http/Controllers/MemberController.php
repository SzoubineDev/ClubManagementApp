<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()
            ->with('roles')
            ->orderBy('name');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('cne', 'like', "%{$search}%");
            });
        }

        if ($team = $request->input('team')) {
            if ($team === 'none') {
                $query->whereNull('team');
            } else {
                $query->where('team', $team);
            }
        }

        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        if ($filiere = $request->input('filiere')) {
            $query->where('filiere', $filiere);
        }

        if ($status = $request->input('status')) {
            if ($status === 'suspended') {
                $query->where('is_suspended', true);
            } elseif ($status === 'active') {
                $query->where('is_suspended', false);
            }
        }

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $members = $query->paginate(20)->withQueryString();

        $roles = Role::orderBy('name')->pluck('name');
        $teams = ['arabic', 'english', 'french'];
        $filieres = ['DEUST', 'LICENSE', 'Cycle Ingénieur', 'Master', 'Doctorat'];

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_suspended', false)->count(),
            'suspended' => User::where('is_suspended', true)->count(),
            'trashed' => User::onlyTrashed()->count(),
        ];

        return view('members.index', compact(
            'members',
            'roles',
            'teams',
            'filieres',
            'stats'
        ));
    }

    public function show(User $member): View
    {
        $this->authorize('view', $member);

        $member->load(['roles', 'permissions']);

        $attendedEvents = $member->events()
            ->wherePivot('attended', true)
            ->orderByDesc('starts_at')
            ->limit(10)
            ->get();

        $upcomingRsvps = $member->events()
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        return view('members.show', compact('member', 'attendedEvents', 'upcomingRsvps'));
    }

    public function edit(User $member): View
    {
        $this->authorize('manage', $member);

        $member->load('roles');

        $roles = Role::orderBy('name')->pluck('name');
        $teams = ['arabic', 'english', 'french'];

        return view('members.edit', compact('member', 'roles', 'teams'));
    }

    public function update(Request $request, User $member): RedirectResponse
    {
        $this->authorize('manage', $member);

        // ---- Team ----
        if ($request->filled('team') || $request->input('team') === '') {
            $this->authorize('updateTeam', $member);

            $request->validate([
                'team' => ['nullable', 'in:arabic,english,french'],
            ]);

            $member->team = $request->input('team') ?: null;
            $member->save();
        }

        // ---- Roles ----
        if ($request->has('roles')) {
            $this->authorize('updateRoles', $member);

            $request->validate([
                'roles' => ['array'],
                'roles.*' => ['string', 'exists:roles,name'],
            ]);

            $newRoles = collect($request->input('roles', []))->unique()->values()->all();

            // President role is untouchable — never removed
            if ($member->hasRole('president') && ! in_array('president', $newRoles)) {
                return back()->withErrors([
                    'roles' => 'The president role cannot be removed.',
                ]);
            }

            $member->syncRoles($newRoles);
        }

        return redirect()
            ->route('members.show', $member)
            ->with('status', 'Member updated.');
    }

    public function suspend(Request $request, User $member): RedirectResponse
    {
        $this->authorize('suspend', $member);

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $member->suspend($request->input('reason'));

        return back()->with('status', "{$member->name} has been suspended.");
    }

    public function unsuspend(User $member): RedirectResponse
    {
        $this->authorize('unsuspend', $member);

        $member->unsuspend();

        return back()->with('status', "{$member->name} has been reactivated.");
    }

    public function destroy(User $member): RedirectResponse
    {
        $this->authorize('delete', $member);

        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('status', "{$member->name} has been deleted.");
    }

    public function restore(User $member): RedirectResponse
    {
        $this->authorize('restore', $member);

        $member->restore();

        return back()->with('status', "{$member->name} has been restored.");
    }
}
