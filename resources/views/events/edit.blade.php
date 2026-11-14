<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <a href="{{ route('events.show', $event) }}" class="breadcrumb-link">← {{ $event->title }}</a>
                <div class="flex items-center gap-3 mt-1 flex-wrap">
                    <h1 class="page-header-title">Edit Event</h1>
                    @if ($event->isPublished())
                        <span class="status-label status-label--on">
                            <span class="status-dot status-dot--on"></span> Published
                        </span>
                    @else
                        <span class="status-label status-label--draft">
                            <span class="status-dot status-dot--draft"></span> Draft
                        </span>
                    @endif
                </div>
                <p class="page-header-sub">Update event schedule, venue, target audience, or capacity.</p>
            </div>
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

            <div class="card-footer flex items-center justify-between gap-3 flex-wrap">
                <a href="{{ route('events.show', $event) }}" class="text-action">Cancel</a>

                <div class="flex gap-2 flex-wrap">
                    @if ($event->isPublished())
                    <button type="submit" name="submit_action" value="draft" class="btn btn-outline">
                        Move to draft
                    </button>
                    @else
                    <button type="submit" name="submit_action" value="draft" class="btn btn-outline">
                        Keep as draft
                    </button>
                    <button type="submit" name="submit_action" value="publish" class="btn btn-success">
                        Publish event
                    </button>
                    @endif

                    <button type="submit" name="submit_action" value="save" class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            </div>
        </form>

    </div>
</x-app-layout>
