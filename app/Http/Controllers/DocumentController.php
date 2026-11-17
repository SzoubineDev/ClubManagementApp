<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
}
