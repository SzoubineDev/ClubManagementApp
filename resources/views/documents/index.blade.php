<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-header-title">Documents</h1>
            <p class="page-header-sub">Official documents for the assistant general</p>
        </div>
    </x-slot>

    <div class="content-wrap content-wrap--mid stack">

        @if ($errors->any())
        <div class="alert alert-danger animate-fade-in-up">{{ $errors->first() }}</div>
        @endif

        <div class="card animate-fade-in-up">
            <div class="card-body stack">
                <div>
                    <h2 class="card-title">Demande de salle</h2>
                    <p class="text-sm text-muted mt-1">
                        Generate a formal room-request letter addressed to the faculty dean.
                        Fill in the details below (all fields are optional) and download the document.
                    </p>
                </div>

                <form method="GET" action="{{ route('documents.room-request') }}" class="stack">
                    <div class="grid grid--2 gap-3">
                        <div class="field">
                            <label for="room" class="field-label static">Room requested</label>
                            <input type="text" id="room" name="room" class="input"
                                value="{{ old('room') }}" placeholder="e.g. Amphithéâtre B">
                        </div>

                        <div class="field">
                            <label for="date" class="field-label static">Date needed</label>
                            <input type="date" id="date" name="date" class="input" value="{{ old('date') }}">
                        </div>

                        <div class="field">
                            <label for="time" class="field-label static">Time</label>
                            <input type="text" id="time" name="time" class="input"
                                value="{{ old('time') }}" placeholder="e.g. 14:00 – 17:00">
                        </div>

                        <div class="field">
                            <label for="purpose" class="field-label static">Purpose</label>
                            <input type="text" id="purpose" name="purpose" class="input"
                                value="{{ old('purpose') }}" placeholder="e.g. Weekly debate training">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download demande de salle
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
