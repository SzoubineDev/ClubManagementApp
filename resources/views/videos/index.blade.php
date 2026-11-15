<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4 w-full flex-wrap">
            <div>
                <h1 class="page-header-title">Media Library</h1>
                <p class="page-header-sub">
                    10 random picks from {{ $total }} videos — reload for a fresh mix
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('videos.index') }}" class="btn btn-outline btn-sm" title="Show another 10 videos">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Shuffle
                </a>
            </div>
        </div>
    </x-slot>

    <div class="content-wrap stack">

        @if (session('status'))
        <div class="alert alert-success animate-fade-in-up">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger animate-fade-in-up">{{ $errors->first() }}</div>
        @endif

        @if ($videos->isEmpty())
        <div class="card animate-fade-in-up">
            <div class="empty-state">
                <p class="empty-state-title">No videos available</p>
                <p>The media library is empty right now.</p>
            </div>
        </div>
        @else
        <div class="video-grid">
            @foreach ($videos as $video)
            <article class="card video-card animate-fade-in-up" data-video-card>
                <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener noreferrer" class="video-thumb-link" aria-label="Watch {{ $video->title }} on YouTube">
                    @if ($video->thumbnail())
                    <img src="{{ $video->thumbnail() }}" alt="{{ $video->title }}" class="video-thumb" loading="lazy">
                    @else
                    <div class="video-thumb video-thumb--fallback"></div>
                    @endif
                    <span class="video-play" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </span>
                </a>

                <div class="card-body video-card-body">
                    <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener noreferrer" class="video-title-link">
                        <h2 class="card-title">{{ $video->title }}</h2>
                    </a>

                    @if ($video->description)
                    <p class="text-sm text-muted video-description">{{ $video->description }}</p>
                    @endif

                    <div class="video-card-footer">
                        <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener noreferrer" class="text-action">
                            Watch on YouTube →
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        @endif

    </div>

    <style>
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }

        .video-card {
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .video-thumb-link {
            position: relative;
            display: block;
            aspect-ratio: 16 / 9;
            background: #111;
            overflow: hidden;
        }

        .video-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 200ms ease;
        }

        .video-thumb--fallback {
            background: linear-gradient(135deg, #1f1f1f, #4a4a4a);
        }

        .video-thumb-link:hover .video-thumb {
            transform: scale(1.04);
        }

        .video-play {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            color: #fff;
            background: rgba(0, 0, 0, 0.25);
        }

        .video-card-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .video-title-link {
            color: inherit;
            text-decoration: none;
        }

        .video-title-link:hover .card-title {
            text-decoration: underline;
        }

        .video-description {
            margin: 0;
        }

        .video-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: auto;
        }
    </style>
</x-app-layout>
