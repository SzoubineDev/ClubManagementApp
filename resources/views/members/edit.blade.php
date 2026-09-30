<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <a href="{{ route('members.show', $member) }}" class="text-sm text-mauve hover:text-bordeaux">
                ← Back to {{ $member->name }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl animate-fade-in-down">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                    </svg>
                    <div>
                        <p class="font-medium text-red-900">Please fix the following:</p>
                        <ul class="mt-1 list-disc list-inside text-sm text-red-800">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            @if (session('status'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl animate-fade-in-down">
                {{ session('status') }}
            </div>
            @endif

            {{-- Member identity card --}}
            <div class="bg-white border border-blush rounded-2xl overflow-hidden animate-fade-in-up">
                <div class="bg-gradient-to-r from-bordeaux to-bordeaux-dark px-6 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-white text-xl font-bold">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-white">{{ $member->name }}</h1>
                            <p class="text-white/80 text-sm mt-0.5">{{ $member->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main edit form --}}
            <form method="POST" action="{{ route('members.update', $member) }}"
                class="bg-white border border-blush rounded-2xl p-6 space-y-6 animate-fade-in-up delay-100">
                @csrf
                @method('PUT')

                {{-- Team --}}
                @can('updateTeam', $member)
                <div x-data="{ selectedTeam: '{{ old('team', $member->team ?? '') }}' }">
                    <label class="block font-medium text-sm text-bordeaux mb-2">Team</label>

                    @php
                    $teamOptions = [
                    '' => ['label' => 'None', 'icon' => '—'],
                    'arabic' => ['label' => 'Arabic', 'icon' => '🌙'],
                    'english' => ['label' => 'English', 'icon' => '🌍'],
                    'french' => ['label' => 'French', 'icon' => '✨'],
                    ];
                    @endphp

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach ($teamOptions as $value => $opt)
                        <label
                            @click="selectedTeam = '{{ $value }}'"
                            :class="selectedTeam === '{{ $value }}'
                        ? 'border-bordeaux bg-blush ring-2 ring-bordeaux/20'
                        : 'border-blush bg-white hover:bg-snow'"
                            class="relative flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition-all">
                            <input type="radio"
                                name="team"
                                value="{{ $value }}"
                                class="sr-only"
                                :checked="selectedTeam === '{{ $value }}'">
                            <span class="text-lg">{{ $opt['icon'] }}</span>
                            <span class="font-medium text-sm text-bordeaux">{{ $opt['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endcan

                {{-- Roles (president only) --}}
                @can('updateRoles', $member)
                <div>
                    <label class="block font-medium text-sm text-bordeaux mb-2">Roles</label>

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

                        <label class="flex items-center gap-3 p-3 border border-blush rounded-xl cursor-pointer hover:bg-snow transition-colors {{ $isLocked ? 'opacity-60 cursor-not-allowed' : '' }}">
                            <input type="checkbox"
                                name="roles[]"
                                value="{{ $role }}"
                                @checked($isChecked)
                                @disabled($isLocked)
                                class="rounded border-blush text-bordeaux focus:ring-bordeaux">

                            <div class="flex-1">
                                <p class="font-medium text-sm text-bordeaux">{{ str_replace('_', ' ', $role) }}</p>
                                @if ($isPresidentRole)
                                <p class="text-xs text-mauve">This role is locked and cannot be removed.</p>
                                @endif
                            </div>

                            @if ($isPresidentRole)
                            <svg class="w-4 h-4 text-bordeaux" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>
                @endcan

                <div class="pt-4 border-t border-blush flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-bordeaux text-white font-semibold rounded-lg hover:bg-bordeaux-light">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>

            {{-- Suspension --}}
            @if (! $member->trashed())
            @can('suspend', $member)
            @if ($member->isSuspended())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 animate-fade-in-up delay-150">
                <h3 class="font-semibold text-red-900">Account suspended</h3>
                <p class="text-sm text-red-700 mt-1">
                    Suspended {{ $member->suspended_at?->diffForHumans() }}.
                    @if ($member->suspension_reason)
                    Reason: "{{ $member->suspension_reason }}"
                    @endif
                </p>

                <form method="POST" action="{{ route('members.unsuspend', $member) }}" class="mt-4">
                    @csrf
                    <button class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                        Reactivate account
                    </button>
                </form>
            </div>
            @else
            <div class="bg-white border border-blush rounded-2xl p-6 animate-fade-in-up delay-150"
                x-data="{ open: false }">
                <h3 class="font-semibold text-bordeaux">Suspend this member</h3>
                <p class="text-sm text-mauve mt-1">
                    Suspended members can log in but lose all capabilities. They'll see a message to contact an administrator.
                </p>

                <button type="button"
                    @click="open = ! open"
                    class="mt-3 px-4 py-2 border border-red-300 text-red-700 text-sm font-medium rounded-lg hover:bg-red-50">
                    Suspend account
                </button>

                <form method="POST" action="{{ route('members.suspend', $member) }}"
                    x-show="open"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="mt-3 space-y-3">
                    @csrf

                    <div>
                        <label class="block font-medium text-sm text-bordeaux mb-1">Reason (optional)</label>
                        <textarea name="reason" rows="2"
                            placeholder="e.g. Attempted unauthorized role assignment"
                            class="w-full px-4 py-3 text-bordeaux bg-snow border border-blush rounded-xl focus:border-bordeaux focus:ring-2 focus:ring-bordeaux/20 focus:outline-none"></textarea>
                    </div>

                    <button class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                        Confirm suspension
                    </button>
                </form>
            </div>
            @endif
            @endcan

            {{-- Delete --}}
            @can('delete', $member)
            <div class="bg-white border border-red-200 rounded-2xl p-6 animate-fade-in-up delay-200">
                <h3 class="font-semibold text-red-900">Delete this member</h3>
                <p class="text-sm text-mauve mt-1">
                    Soft-deletes the account. Data is retained and can be restored by a president. The member won't be able to log in.
                </p>

                <form method="POST" action="{{ route('members.destroy', $member) }}"
                    onsubmit="return confirm('Delete {{ $member->name }}? This can be undone by a president.')"
                    class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                        Delete member
                    </button>
                </form>
            </div>
            @endcan
            @else
            {{-- Restore --}}
            @can('restore', $member)
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 animate-fade-in-up delay-150">
                <h3 class="font-semibold text-yellow-900">This member is deleted</h3>
                <p class="text-sm text-yellow-700 mt-1">
                    Deleted {{ $member->deleted_at->diffForHumans() }}.
                </p>

                <form method="POST" action="{{ route('members.restore', $member) }}" class="mt-3">
                    @csrf
                    <button class="px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700">
                        Restore member
                    </button>
                </form>
            </div>
            @endcan
            @endif

        </div>
    </div>
</x-app-layout>