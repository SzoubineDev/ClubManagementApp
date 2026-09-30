<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-bordeaux leading-tight">
            Account Suspended
        </h2>
    </x-slot>

    <div class="py-16">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm border border-blush rounded-2xl p-8 text-center animate-fade-in-up">
                <div class="mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center text-red-600 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-bordeaux">Your account is suspended</h1>

                <p class="mt-4 text-mauve">
                    Your access to club features has been temporarily restricted.
                    Please contact a club administrator for more information.
                </p>

                @if (auth()->user()->suspension_reason)
                <div class="mt-6 p-4 bg-snow border border-blush rounded-xl text-left">
                    <p class="text-xs uppercase tracking-wide text-mauve font-medium">Reason</p>
                    <p class="mt-1 text-sm text-bordeaux">{{ auth()->user()->suspension_reason }}</p>
                </div>
                @endif

                @if (auth()->user()->suspended_at)
                <p class="mt-4 text-xs text-mauve">
                    Suspended on {{ auth()->user()->suspended_at->format('F j, Y') }}
                </p>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="mt-8">
                    @csrf
                    <button type="submit"
                        class="px-6 py-2.5 border border-blush text-bordeaux font-medium rounded-lg hover:bg-snow">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>