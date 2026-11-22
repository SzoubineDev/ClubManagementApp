<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4 w-full flex-wrap">
            <div>
                <h1 class="page-header-title">Applications</h1>
                <p class="page-header-sub">Review recruitment applications</p>
            </div>
            <span class="chip chip--brand" id="applications-total-count" data-total="{{ $counts['all'] }}">
                {{ $counts['all'] }} {{ Str::plural('application', $counts['all']) }}
            </span>
        </div>
    </x-slot>

    <div class="content-wrap stack">

        {{-- Toast notifications (JS actions) --}}
        <div id="app-toasts" class="toast-stack" aria-live="polite"></div>

        @if (session('status'))
        <div class="alert alert-success animate-fade-in-up">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger animate-fade-in-up">{{ $errors->first() }}</div>
        @endif

        {{-- Statistics KPI strip --}}
        <div class="kpi-grid kpi-grid--5 stagger">
            <div class="kpi-card">
                <p class="stat-label">Total Applications</p>
                <p class="stat-value tabular mt-1" data-kpi="all">{{ $counts['all'] }}</p>
            </div>
            <div class="kpi-card">
                <p class="stat-label">Pending</p>
                <p class="stat-value tabular mt-1" style="color: #8C6200;" data-kpi="pending">{{ $counts['pending'] }}</p>
            </div>
            <div class="kpi-card">
                <p class="stat-label">Passed to Test</p>
                <p class="stat-value tabular mt-1" style="color: #3B3470;" data-kpi="passed_to_test">{{ $counts['passed_to_test'] }}</p>
            </div>
            <div class="kpi-card">
                <p class="stat-label">Accepted</p>
                <p class="stat-value tabular mt-1" style="color: #0F5148;" data-kpi="accepted">{{ $counts['accepted'] }}</p>
            </div>
            <div class="kpi-card">
                <p class="stat-label">Rejected</p>
                <p class="stat-value tabular mt-1" style="color: var(--nb-burgundy, #660C1B);" data-kpi="rejected">{{ $counts['rejected'] }}</p>
            </div>
        </div>

        {{-- Status filters + search --}}
        <div class="card animate-fade-in-up">
            <div class="card-body" style="display: flex; flex-direction: column; gap: 18px;">

                @php
                    $tabs = [
                        'all' => 'All',
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'passed_to_test' => 'Passed to test',
                    ];
                    $current = $activeStatus ?? 'all';

                    $sectionTabs = ['all' => 'All sections'] + \App\Models\UserRecrute::SECTION_LABELS;
                @endphp

                @if ($canFilterSections)
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="stat-label" style="margin: 0;">Section</span>
                    <nav class="app-filter-tabs" aria-label="Filter applications by section">
                        @foreach ($sectionTabs as $key => $label)
                        <a
                            href="{{ route('applications.index', array_filter([
                                'section' => $key === 'all' ? null : $key,
                                'status' => $activeStatus,
                                'q' => $search ?: null,
                            ])) }}"
                            class="app-filter-tab {{ $activeSection === ($key === 'all' ? null : $key) ? 'is-active' : '' }}"
                            @if ($activeSection === ($key === 'all' ? null : $key)) aria-current="page" @endif>
                            {{ $label }}
                        </a>
                        @endforeach
                    </nav>
                </div>
                @endif

                <nav class="app-filter-tabs" aria-label="Filter applications by status">
                    @foreach ($tabs as $key => $label)
                    <a
                        href="{{ route('applications.index', array_filter(['status' => $key === 'all' ? null : $key, 'q' => $search ?: null, 'section' => $activeSection])) }}"
                        class="app-filter-tab {{ $current === $key ? 'is-active' : '' }}"
                        @if ($current === $key) aria-current="page" @endif>
                        {{ $label }}
                        <span class="app-filter-tab-count" data-count="{{ $key }}">{{ $counts[$key] }}</span>
                    </a>
                    @endforeach
                </nav>

                <form method="GET" action="{{ route('applications.index') }}" class="filter-bar">
                    @if ($activeStatus)
                    <input type="hidden" name="status" value="{{ $activeStatus }}">
                    @endif
                    @if ($activeSection)
                    <input type="hidden" name="section" value="{{ $activeSection }}">
                    @endif

                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search by name, email, or CNE"
                        class="input"
                        style="flex: 1 1 260px;"
                        aria-label="Search applications">

                    <button type="submit" class="btn btn-primary btn-sm">Search</button>

                    @if ($search || $activeStatus || $activeSection)
                    <a href="{{ route('applications.index') }}" class="text-action">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Applications table --}}
        <div class="card overflow-hidden animate-fade-in-up delay-100">

            <div class="table-wrap" id="applications-table" @if ($applications->isEmpty()) hidden @endif>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th class="hide-mobile col-secondary">Phone</th>
                            <th class="hide-mobile col-optional">CNE</th>
                            <th class="hide-mobile col-optional">Section</th>
                            <th class="hide-mobile col-optional">Filière</th>
                            <th class="hide-mobile col-secondary">Submitted</th>
                            <th>Status</th>
                            <th class="td-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="applications-body">
                        @foreach ($applications as $application)
                        <tr data-application-row="{{ $application->id }}" data-status="{{ $application->status }}">
                            <td>
                                <div class="min-w-0">
                                    <div class="cell-title truncate">{{ $application->full_name }}</div>
                                    <p class="cell-sub truncate">{{ $application->email }}</p>
                                    <p class="cell-sub hide-desktop" style="display: none; margin-top: 2px; font-size: 11.5px;">
                                        {{ $application->cne }} · {{ $application->filiere }}
                                    </p>
                                </div>
                            </td>

                            <td class="hide-mobile col-secondary tabular">{{ $application->phone_number }}</td>
                            <td class="hide-mobile col-optional tabular">{{ $application->cne }}</td>
                            <td class="hide-mobile col-optional">{{ $application->sectionLabel() }}</td>
                            <td class="hide-mobile col-optional">{{ $application->filiere }}</td>
                            <td class="hide-mobile col-secondary tabular">{{ $application->created_at?->format('M j, Y') }}</td>

                            <td>
                                <span class="status-badge {{ $application->statusBadgeClass() }}" data-status-badge>
                                    {{ $application->statusLabel() }}
                                </span>
                            </td>

                            <td class="td-right td-nowrap">
                                <div class="app-actions">
                                    <button
                                        type="button"
                                        class="text-action"
                                        data-view
                                        data-name="{{ $application->full_name }}"
                                        data-email="{{ $application->email }}"
                                        data-phone="{{ $application->phone_number }}"
                                        data-cne="{{ $application->cne }}"
                                        data-section="{{ $application->sectionLabel() }}"
                                        data-filiere="{{ $application->filiere }}"
                                        data-status-label="{{ $application->statusLabel() }}"
                                        data-created="{{ $application->created_at?->format('M j, Y · H:i') }}"
                                        data-motivation="{{ $application->motivation }}">View</button>

                                    @can('updateStatus', $application)
                                    <button
                                        type="button"
                                        class="manage-trigger"
                                        data-manage
                                        data-name="{{ $application->full_name }}"
                                        data-status-url="{{ route('applications.status', $application) }}"
                                        data-delete-url="{{ route('applications.destroy', $application) }}">
                                        Manage
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="empty-state" id="applications-empty" @if ($applications->isNotEmpty()) hidden @endif>
                <p class="empty-state-title">
                    {{ $search || $activeStatus || $activeSection ? 'No matching applications' : 'No applications yet' }}
                </p>
                <p>
                    {{ $search || $activeStatus || $activeSection
                        ? 'Try adjusting or clearing your filters.'
                        : 'New recruitment applications will appear here as candidates apply.' }}
                </p>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($applications->hasPages())
        <div>{{ $applications->links() }}</div>
        @endif

    </div>

    <script>
        (function () {
            // The page script is parsed before the deferred `app.js` module runs,
            // so `window.TGD` may not exist yet. `init()` runs once the DOM (and
            // therefore the module scripts) have finished loading.
            function init() {
                const TGD = window.TGD;
                if (!TGD) return;

                const csrf = TGD.csrf;
                const activeFilter = @json($activeStatus);

                // Human-readable copy + target status for each action.
                const COPY = {
                    accept: {
                        status: 'accepted',
                        title: 'Accept application?',
                        message: (name) => `Are you sure you want to accept ${name}'s application? The applicant will be marked as accepted.`,
                        confirmLabel: 'Confirm acceptance',
                    },
                    reject: {
                        status: 'rejected',
                        title: 'Reject application?',
                        message: (name) => `Are you sure you want to reject ${name}'s application? The applicant will be marked as rejected.`,
                        confirmLabel: 'Confirm rejection',
                    },
                    pass: {
                        status: 'passed_to_test',
                        title: 'Pass application to test?',
                        message: (name) => `Are you sure you want to pass ${name}'s application to the recruitment test? The applicant will be marked as passed to test.`,
                        confirmLabel: 'Pass to test',
                    },
                    delete: {
                        status: null,
                        title: 'Delete application?',
                        message: (name) => `Are you sure you want to permanently delete ${name}'s application? This action cannot be undone.`,
                        confirmLabel: 'Delete permanently',
                        danger: true,
                    },
                };

                // Icons for the Manage card (static, trusted markup).
                const svg = (path) => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + path + '</svg>';
                const ICONS = {
                    accept: svg('<path d="M5 12.5l4.5 4.5L19 7"/>'),
                    reject: svg('<path d="M6 6l12 12M18 6L6 18"/>'),
                    pass: svg('<path d="M5 12h14M13 6l6 6-6 6"/>'),
                    delete: svg('<path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13"/>'),
                };

                // Operations shown in the centered Manage card, in order.
                const MENU_ITEMS = [
                    { key: 'accept', label: 'Accept application', hint: 'Mark this applicant as accepted', icon: ICONS.accept },
                    { key: 'reject', label: 'Reject application', hint: 'Mark this applicant as rejected', icon: ICONS.reject },
                    { key: 'pass', label: 'Pass to test', hint: 'Advance to the recruitment test', icon: ICONS.pass },
                    { key: 'delete', label: 'Delete application', hint: 'Permanently remove — cannot be undone', icon: ICONS.delete, danger: true },
                ];

                async function request(url, method, body) {
                    const res = await fetch(url, {
                        method,
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: body ? JSON.stringify(body) : undefined,
                    });

                    let data = {};
                    try { data = await res.json(); } catch (e) { /* empty body */ }

                    if (!res.ok) {
                        let message = 'Request failed (' + res.status + ')';
                        if (data.errors) {
                            const first = Object.values(data.errors)[0];
                            if (Array.isArray(first) && first[0]) message = first[0];
                        } else if (data.message) {
                            message = data.message;
                        }
                        throw new Error(message);
                    }

                    return data;
                }

                function adjustCount(status, delta) {
                    const el = document.querySelector('[data-count="' + status + '"]');
                    if (el) {
                        el.textContent = Math.max(0, (parseInt(el.textContent, 10) || 0) + delta);
                    }
                    const kpi = document.querySelector('[data-kpi="' + status + '"]');
                    if (kpi) {
                        kpi.textContent = Math.max(0, (parseInt(kpi.textContent, 10) || 0) + delta);
                    }
                    const headerChip = document.getElementById('applications-total-count');
                    if (headerChip && status === 'all') {
                        const total = Math.max(0, (parseInt(headerChip.dataset.total, 10) || 0) + delta);
                        headerChip.dataset.total = total;
                        headerChip.textContent = total + (total === 1 ? ' application' : ' applications');
                    }
                }

                function syncEmptyState() {
                    const body = document.getElementById('applications-body');
                    const table = document.getElementById('applications-table');
                    const empty = document.getElementById('applications-empty');
                    if (!body || !table || !empty) return;

                    const hasRows = body.querySelector('tr') !== null;
                    table.hidden = !hasRows;
                    empty.hidden = hasRows;
                }

                // Runs one operation: confirm, request, then update the row + counts.
                async function performAction(action, ctx) {
                    const copy = COPY[action];
                    if (!copy) return;

                    const confirmed = await TGD.confirm({
                        title: copy.title,
                        subject: ctx.name,
                        message: copy.message(ctx.name),
                        confirmLabel: copy.confirmLabel,
                        danger: !!copy.danger,
                    });
                    if (!confirmed) return;

                    try {
                        let data;

                        if (action === 'delete') {
                            data = await request(ctx.deleteUrl, 'DELETE');

                            const rowStatus = ctx.row ? ctx.row.dataset.status : null;
                            if (ctx.row) ctx.row.remove();

                            adjustCount('all', -1);
                            if (rowStatus) adjustCount(rowStatus, -1);
                            syncEmptyState();
                        } else {
                            data = await request(ctx.statusUrl, 'PATCH', { status: copy.status });

                            const row = ctx.row;
                            if (row) {
                                const oldStatus = row.dataset.status;
                                const newStatus = data.status;
                                row.dataset.status = newStatus;

                                const badge = row.querySelector('[data-status-badge]');
                                if (badge) {
                                    badge.className = 'status-badge ' + data.status_class;
                                    badge.textContent = data.status_label;
                                }

                                if (oldStatus !== newStatus) {
                                    adjustCount(oldStatus, -1);
                                    adjustCount(newStatus, 1);
                                }

                                // Drop the row if it no longer matches the active filter.
                                if (activeFilter && activeFilter !== newStatus) {
                                    row.remove();
                                    syncEmptyState();
                                }
                            }
                        }

                        TGD.toast(data.message || 'Done.', 'success');
                    } catch (err) {
                        TGD.toast(err.message || 'Something went wrong.', 'error');
                    }
                }

                document.addEventListener('click', async (event) => {
                    // --- Detail view ---
                    const viewBtn = event.target.closest('[data-view]');
                    if (viewBtn) {
                        event.preventDefault();
                        const d = viewBtn.dataset;

                        TGD.info({
                            title: d.name,
                            subtitle: 'Recruitment application',
                            fields: [
                                { label: 'Full name', value: d.name },
                                { label: 'Email', value: d.email },
                                { label: 'Phone', value: d.phone },
                                { label: 'CNE', value: d.cne },
                                { label: 'Section', value: d.section },
                                { label: 'Filière', value: d.filiere },
                                { label: 'Status', value: d.statusLabel },
                                { label: 'Submitted', value: d.created },
                                { label: 'Motivation', value: d.motivation, wide: true },
                            ],
                        });
                        return;
                    }

                    // --- Manage card: pick an operation, then confirm it ---
                    const manageBtn = event.target.closest('[data-manage]');
                    if (!manageBtn) return;
                    event.preventDefault();

                    if (manageBtn.disabled) return;
                    manageBtn.disabled = true;

                    const name = manageBtn.dataset.name || 'this applicant';

                    try {
                        const action = await TGD.menu({
                            title: 'Manage application',
                            subject: name,
                            options: MENU_ITEMS,
                        });

                        if (action) {
                            await performAction(action, {
                                name,
                                statusUrl: manageBtn.dataset.statusUrl,
                                deleteUrl: manageBtn.dataset.deleteUrl,
                                row: manageBtn.closest('tr'),
                            });
                        }
                    } finally {
                        manageBtn.disabled = false;
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
</x-app-layout>
