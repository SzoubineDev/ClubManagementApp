<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <a href="{{ route('members.show', $member) }}" class="breadcrumb-link">← Back to profile</a>
                <h1 class="page-header-title">Edit member</h1>
                <p class="page-header-sub">{{ $member->name }} · {{ $member->email }}</p>
            </div>
        </div>
    </x-slot>

    <div class="content-wrap content-wrap--narrow">
        <div class="stack">

            @if ($errors->any())
            <div class="alert alert-danger animate-fade-in-down">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                </svg>
                <div>
                    <p class="alert-title">Please fix the following:</p>
                    <ul class="list">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            @if (session('status'))
            <div class="alert alert-success animate-fade-in-down">{{ session('status') }}</div>
            @endif

            {{-- Member identity banner --}}
            <div class="identity-banner animate-fade-in-up">
                <div class="avatar avatar--xl">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                <div class="min-w-0">
                    <h1 class="identity-name truncate">{{ $member->name }}</h1>
                    <p class="identity-email truncate">{{ $member->email }}</p>
                </div>
            </div>

            {{-- Main edit form --}}
            <form method="POST" action="{{ route('members.update', $member) }}" class="card animate-fade-in-up delay-100">
                @csrf
                @method('PUT')

                <div class="card-body space-y-6">

                    {{-- Team --}}
                    @can('updateTeam', $member)
                    <div x-data="{ selectedTeam: '{{ old('team', $member->team ?? '') }}' }">
                        <label class="field-label static">Team</label>

                        @php
                        $teamOptions = [
                        '' => ['label' => 'None', 'icon' => '—'],
                        'arabic' => ['label' => 'Arabic', 'icon' => '🌙'],
                        'english' => ['label' => 'English', 'icon' => '🌍'],
                        'french' => ['label' => 'French', 'icon' => '✨'],
                        ];
                        @endphp

                        <div class="select-grid select-grid--4">
                            @foreach ($teamOptions as $value => $opt)
                            <label @click="selectedTeam = '{{ $value }}'" :class="selectedTeam === '{{ $value }}' ? 'is-selected' : ''"
                                class="select-card">
                                <input type="radio" name="team" value="{{ $value }}" class="sr-only"
                                    :checked="selectedTeam === '{{ $value }}'">
                                <span class="select-card-icon">{{ $opt['icon'] }}</span>
                                <span class="select-card-title">{{ $opt['label'] }}</span>
                                <span class="select-card-check">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endcan

                    {{-- Roles (president only) --}}
                    @can('updateRoles', $member)
                    <div>
                        <label class="field-label static">Roles</label>

                        @php
                        $allRoles = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name');
                        $currentRoles = old('roles', $member->roles->pluck('name')->toArray());
                        @endphp

                        <div class="space-y-2">
                            @foreach ($allRoles as $role)
                            @php
                            $isPresidentRole = $role === 'president';
                            $isChecked = in_array($role, $currentRoles);
                            $isLocked = $isPresidentRole && $member->hasRole('president');
                            @endphp

                            <label class="option-row {{ $isLocked ? 'is-locked' : '' }}">
                                <input type="checkbox" name="roles[]" value="{{ $role }}" @checked($isChecked)
                                    @disabled($isLocked) class="checkbox">

                                <div class="option-row-main">
                                    <p class="option-row-title">{{ str_replace('_', ' ', $role) }}</p>
                                    @if ($isPresidentRole)
                                    <p class="option-row-desc">This role is locked and cannot be removed.</p>
                                    @endif
                                </div>

                                @if ($isPresidentRole)
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-brand">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endcan
                </div>

                <div class="card-footer card-footer-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save changes
                    </button>
                </div>
            </form>

            {{-- Suspension --}}
            @if (! $member->trashed())
            @can('suspend', $member)
            @if ($member->isSuspended())
            <div class="notice-zone animate-fade-in-up delay-200">
                <h3 class="card-title text-brand">Account suspended</h3>
                <p class="text-sm text-muted mt-1">
                    Suspended {{ $member->suspended_at?->diffForHumans() }}.
                    @if ($member->suspension_reason)
                    Reason: "{{ $member->suspension_reason }}"
                    @endif
                </p>

                <form method="POST" action="{{ route('members.unsuspend', $member) }}" class="mt-4">
                    @csrf
                    <button class="btn btn-success btn-sm">Reactivate account</button>
                </form>
            </div>
            @else
            <div class="notice-zone animate-fade-in-up delay-200" x-data="{ open: false }">
                <h3 class="card-title text-brand">Suspend this member</h3>
                <p class="text-sm text-muted mt-1">
                    Suspended members can log in but lose all capabilities. They'll see a message to contact an administrator.
                </p>

                <button type="button" @click="open = ! open" class="btn btn-outline btn-sm mt-4">Suspend account</button>

                <form method="POST" action="{{ route('members.suspend', $member) }}" x-show="open" x-cloak
                    x-transition.opacity.duration.200ms class="mt-4 space-y-3" style="display:none;">
                    @csrf

                    <div class="field" style="width:100%">
                        <textarea name="reason" rows="2" id="reason" class="field-input"
                            placeholder="e.g. Attempted unauthorized role assignment"></textarea>
                        <label for="reason" class="field-label">Reason (optional)</label>
                    </div>

                    <button class="btn btn-warn btn-sm">Confirm suspension</button>
                </form>
            </div>
            @endif
            @endcan

            {{-- Delete --}}
            @can('delete', $member)
            <div class="notice-zone animate-fade-in-up delay-300">
                <h3 class="card-title text-brand">Delete this member</h3>
                <p class="text-sm text-muted mt-1">
                    Soft-deletes the account. Data is retained and can be restored by a president. The member won't be able to log in.
                </p>

                <form method="POST" action="{{ route('members.destroy', $member) }}"
                    onsubmit="return confirm('Delete {{ $member->name }}? This can be undone by a president.')" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-warn btn-sm">Delete member</button>
                </form>
            </div>
            @endcan
            @else
            {{-- Restore --}}
            @can('restore', $member)
            <div class="notice-zone animate-fade-in-up delay-200">
                <h3 class="card-title text-brand">This member is deleted</h3>
                <p class="text-sm text-muted mt-1">Deleted {{ $member->deleted_at->diffForHumans() }}.</p>

                <form method="POST" action="{{ route('members.restore', $member) }}" class="mt-4">
                    @csrf
                    <button class="btn btn-warn btn-sm">Restore member</button>
                </form>
            </div>
            @endcan
            @endif

        </div>
    </div>
</x-app-layout>
