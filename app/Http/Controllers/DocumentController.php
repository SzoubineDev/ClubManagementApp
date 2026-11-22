<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Official documents available to the assistant general.
 *
 * Documents are generated server-side from a Blade template and downloaded
 * as PDF files via barryvdh/laravel-dompdf.
 */
class DocumentController extends Controller
{
    public function index(): View
    {
        $this->authorize('documents.download');

        return view('documents.index');
    }

    public function downloadRoomRequest(Request $request): Response
    {
        $this->authorize('documents.download');

        $this->ensurePdfStorageIsWritable();

        $validated = $request->validate([
            'room' => ['nullable', 'string', 'max:120'],
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'string', 'max:60'],
            'purpose' => ['nullable', 'string', 'max:180'],
        ]);

        $requestedDate = null;

        if (! empty($validated['date'])) {
            $requestedDate = Carbon::parse($validated['date'])->translatedFormat('d F Y');
        }

        $pdf = Pdf::loadView('documents.room-request', [
            'requester' => $request->user(),
            'room' => $validated['room'] ?? null,
            'requestedDate' => $requestedDate,
            'time' => $validated['time'] ?? null,
            'purpose' => $validated['purpose'] ?? null,
            'today' => now()->translatedFormat('d F Y'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('demande-de-salle.pdf');
    }

    /**
     * dompdf caches font metrics inside storage/fonts. That directory is not
     * tracked in git and storage is usually excluded from deployments, so a
     * fresh production server may not have it. Create it on demand instead of
     * letting the PDF request fail with a "cannot write font cache" error.
     */
    private function ensurePdfStorageIsWritable(): void
    {
        $directories = array_filter(array_unique([
            config('dompdf.options.font_dir'),
            config('dompdf.options.font_cache'),
        ]), fn ($directory) => is_string($directory) && $directory !== '');

        foreach ($directories as $directory) {
            try {
                File::ensureDirectoryExists($directory, 0775, true);
            } catch (\Throwable $exception) {
                Log::warning('Unable to create the dompdf storage directory.', [
                    'directory' => $directory,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}
