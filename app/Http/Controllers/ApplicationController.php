<?php

namespace App\Http\Controllers;

use App\Models\UserRecrute;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Number of applications shown per page.
     */
    private const PER_PAGE = 15;

    /**
     * Prefix used when exporting applications with anonymous group names.
     */
    private const GROUP_NAME_PREFIX = 'tgd27_';

    /**
     * Applications management dashboard: search, status filter, counts, pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', UserRecrute::class);

        $user = $request->user();

        // Only trust a status that is actually allowed.
        $status = $request->input('status');
        if (! in_array($status, UserRecrute::STATUSES, true)) {
            $status = null;
        }

        $search = trim((string) $request->input('q'));

        // Managers (president, vice-president, secretary general) can browse
        // every section and narrow the list with the section filter. A section
        // lead is always locked to the section they lead.
        $canFilterSections = $user->isManager();

        $section = $canFilterSections
            ? $request->input('section')
            : $user->recruitmentSection();

        if (! in_array($section, UserRecrute::SECTIONS, true)) {
            $section = null;
        }

        $query = $this->applicationsQuery($section);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('cne', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $applications = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $counts = $this->statusCounts($section);

        return view('applications.index', [
            'applications' => $applications,
            'counts' => $counts,
            'activeStatus' => $status,
            'activeSection' => $section,
            'canFilterSections' => $canFilterSections,
            'search' => $search,
        ]);
    }

    /**
     * Download recruitment applications as a PDF file.
     *
     * Managers can export every application or a single debate section.
     * Section leads are intentionally excluded: they only review their own
     * section and this endpoint would otherwise expose the whole database.
     */
    public function export(Request $request): Response
    {
        $this->authorize('viewAny', UserRecrute::class);

        abort_unless(
            $request->user()->isManager(),
            403,
            'Only management roles can export applications.'
        );

        $this->ensurePdfStorageIsWritable();

        $section = $request->input('section');

        if (! in_array($section, UserRecrute::SECTIONS, true)) {
            $section = null;
        }

        $naming = $request->input('naming');

        if (! in_array($naming, ['normal', 'group'], true)) {
            $naming = 'normal';
        }

        $applications = $this->applicationsQuery($section)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $label = $section !== null
            ? strtolower(UserRecrute::SECTION_LABELS[$section])
            : 'all';

        $filename = "applications-{$label}"
            .($naming === 'group' ? '-group' : '')
            .'-'.now()->format('Y-m-d').'.pdf';

        $pdf = Pdf::loadView('applications.export', [
            'applications' => $applications,
            'total' => $applications->count(),
            'sectionLabel' => $section !== null
                ? UserRecrute::SECTION_LABELS[$section].' section'
                : 'All sections',
            'generatedAt' => now()->format('M j, Y · H:i'),
            'groupNaming' => $naming === 'group',
            'groupNamePrefix' => self::GROUP_NAME_PREFIX,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Change an application's review status (accept / reject / pass to test).
     */
    public function updateStatus(Request $request, UserRecrute $application): JsonResponse|RedirectResponse
    {
        $this->authorize('updateStatus', $application);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', UserRecrute::STATUSES)],
        ]);

        $application->update(['status' => $validated['status']]);

        $message = "{$application->full_name}'s application was marked as {$application->statusLabel()}.";

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'status' => $application->status,
                'status_label' => $application->statusLabel(),
                'status_class' => $application->statusBadgeClass(),
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Permanently delete an application.
     */
    public function destroy(Request $request, UserRecrute $application): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $application);

        $name = $application->full_name;
        $application->delete();

        $message = "{$name}'s application has been deleted.";

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $message]);
        }

        return redirect()
            ->route('applications.index')
            ->with('status', $message);
    }

    /**
     * dompdf writes font metrics to storage/fonts, which may not exist on a
     * fresh deployment. Create it on demand so PDF exports never fail there.
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

    /**
     * Base query for applications, optionally limited to one section.
     */
    private function applicationsQuery(?string $section = null): Builder
    {
        return UserRecrute::query()
            ->when($section !== null, fn ($q) => $q->where('preferred_section', $section));
    }

    /**
     * Application totals grouped by status, plus the grand total.
     *
     * When a section is given, only that section's applications are counted
     * so the dashboard KPIs never leak other sections' totals.
     *
     * @return array<string, int>
     */
    private function statusCounts(?string $section = null): array
    {
        $query = UserRecrute::query();

        if ($section !== null) {
            $query->where('preferred_section', $section);
        }

        $rows = $query
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $counts = ['all' => (int) $rows->sum()];

        foreach (UserRecrute::STATUSES as $status) {
            $counts[$status] = (int) ($rows[$status] ?? 0);
        }

        return $counts;
    }
}
