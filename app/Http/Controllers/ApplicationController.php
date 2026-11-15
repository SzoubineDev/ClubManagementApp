<?php

namespace App\Http\Controllers;

use App\Models\UserRecrute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Number of applications shown per page.
     */
    private const PER_PAGE = 15;

    /**
     * Applications management dashboard: search, status filter, counts, pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', UserRecrute::class);

        // Only trust a status that is actually allowed.
        $status = $request->input('status');
        if (! in_array($status, UserRecrute::STATUSES, true)) {
            $status = null;
        }

        $search = trim((string) $request->input('q'));

        // Managers keep full visibility; a section lead only sees applicants
        // who picked their own section.
        $section = $request->user()->isManager()
            ? null
            : $request->user()->recruitmentSection();

        $query = UserRecrute::query();

        if ($section !== null) {
            $query->where('preferred_section', $section);
        }

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
            'search' => $search,
        ]);
    }

    /**
     * Change an application's review status (accept / reject / pass to test).
     */
    public function updateStatus(Request $request, UserRecrute $application): JsonResponse|RedirectResponse
    {
        $this->authorize('updateStatus', $application);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', UserRecrute::STATUSES)],
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
