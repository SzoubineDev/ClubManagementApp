<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Official documents available to the assistant general.
 *
 * Documents are generated server-side and downloaded as Word-compatible
 * files, so no PDF package is required.
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

        $content = view('documents.room-request', [
            'requester' => $request->user(),
            'room' => $validated['room'] ?? null,
            'requestedDate' => $requestedDate,
            'time' => $validated['time'] ?? null,
            'purpose' => $validated['purpose'] ?? null,
            'today' => now()->translatedFormat('d F Y'),
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="demande-de-salle.doc"',
        ]);
    }
}
