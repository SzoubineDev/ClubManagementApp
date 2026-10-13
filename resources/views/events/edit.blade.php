<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('events.show', $event) }}" class="breadcrumb-link">← {{ $event->title }}</a>
            <h1 class="page-header-title mt-1">Edit event</h1>
            <p class="page-header-sub">Update event details</p>
        </div>
    </x-slot>

    <div class="content-wrap content-wrap--mid stack">

        @if ($errors->any())
        <div class="alert alert-danger animate-fade-in-down">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <div>
                <p class="alert-title">Please fix the following:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('events.update', $event) }}" id="events-form" class="card animate-fade-in-up">
            @csrf
            @method('PUT')

            @include('events._form', ['event' => $event])

            <div class="card-footer">
                <a href="{{ route('events.show', $event) }}" class="text-action">Cancel</a>

                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save changes
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
