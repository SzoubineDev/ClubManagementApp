<x-guest-layout>
    <div class="card notice-card animate-fade-in-up">
        <div class="notice-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <h1 class="text-2xl font-semibold text-ink">Account Suspended</h1>
        <p class="text-muted mt-2 leading-relaxed">Your account has been suspended. Contact administration if you think
            this is an error.</p>

        @if (!empty($reason))
        <div class="notice-reason">
            <p class="field-caption">Reason</p>
            <p class="text-sm mt-1">{{ $reason }}</p>
        </div>
        @endif

        <div class="mt-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline">Sign out</button>
            </form>
        </div>
    </div>
</x-guest-layout>
