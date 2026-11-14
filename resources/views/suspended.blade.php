<x-guest-layout :wide="true">
    <div class="suspend-container animate-fade-in-up">
        {{-- Header pill badge --}}
        <div class="mb-4">
            <span class="suspend-badge">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 9v3.75m0 3.75h.007M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                OFFICIAL NOTICE · MEMBERSHIP ON HOLD
            </span>
        </div>

        {{-- Big Title --}}
        <div class="mb-6">
            <h1 class="auth-title suspend-title">Account Suspended</h1>
            <p class="auth-sub mt-2">
                Your club membership access has been placed on hold by the President.
            </p>
        </div>

        {{-- Member profile card (if authenticated / details available) --}}
        @if ($user || !empty($userName))
        @php
            $mName = $user ? $user->name : ($userName ?? 'Member');
            $mEmail = $user ? $user->email : ($userEmail ?? '');
            $mCne = $user ? $user->cne : null;
            $mTeam = $user ? $user->team : null;
            $mFiliere = $user ? $user->filiere : null;
            $mDate = $suspended_at ?? ($user ? $user->suspended_at : null);
        @endphp
        <div class="suspend-member-card">
            <div class="suspend-avatar">
                {{ strtoupper(substr($mName, 0, 1)) }}
            </div>
            <div class="suspend-member-info">
                <h3 class="suspend-member-name">{{ $mName }}</h3>
                <p class="suspend-member-meta">{{ $mEmail }} @if($mCne)· CNE: {{ $mCne }}@endif</p>
                <div class="suspend-member-tags">
                    @if($mTeam)
                        <span class="suspend-tag suspend-tag--team">{{ ucfirst($mTeam) }} team</span>
                    @endif
                    @if($mFiliere)
                        <span class="suspend-tag">{{ $mFiliere }}</span>
                    @endif
                    @if($mDate)
                        <span class="suspend-tag suspend-tag--date">
                            Suspended {{ \Carbon\Carbon::parse($mDate)->diffForHumans() }}
                            ({{ \Carbon\Carbon::parse($mDate)->format('M d, Y') }})
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Reason callout box --}}
        <div class="suspend-reason-card">
            <div class="suspend-reason-header">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h6m-8 8l4-4h10a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14z" />
                </svg>
                <span>President's Note / Reason</span>
            </div>
            @if (!empty($reason))
                <blockquote class="suspend-reason-body">
                    "{{ $reason }}"
                </blockquote>
            @else
                <p class="suspend-reason-empty">
                    No specific remark was recorded in the suspension log. Please contact the Club President or the Executive Board for full clarification.
                </p>
            @endif
        </div>

        {{-- Impact notice --}}
        <div class="suspend-restrictions">
            <h4 class="suspend-restrictions-title">Current restrictions in effect:</h4>
            <ul class="suspend-restrictions-list">
                <li>
                    <span class="suspend-x">✕</span>
                    <span><strong>Event RSVPs & Attendance:</strong> Inability to register for or check into club debates & workshops.</span>
                </li>
                <li>
                    <span class="suspend-x">✕</span>
                    <span><strong>Internal Dashboard:</strong> Access to club management, minutes, and team portals is locked.</span>
                </li>
                <li>
                    <span class="suspend-x">✕</span>
                    <span><strong>Account Privileges:</strong> Profile updates and member voting are disabled during suspension.</span>
                </li>
            </ul>
        </div>

        {{-- Contact advice --}}
        <div class="suspend-help">
            <p>
                <strong>Need to appeal?</strong> Reach out directly to the Club President or email us at
                <a href="mailto:tgd.club.fst@gmail.com" class="suspend-link">tgd.club.fst@gmail.com</a>.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="suspend-actions">
            @if (auth()->check())
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="btn btn-outline suspend-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sign out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary suspend-btn">
                    Sign in with another account
                </a>
                <a href="/" class="btn btn-outline suspend-btn">
                    Return to homepage
                </a>
            @endif
        </div>
    </div>
</x-guest-layout>
