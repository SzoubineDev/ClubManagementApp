 <x-app-layout>
     <x-slot name="header">
         <div class="flex items-start justify-between gap-4 w-full flex-wrap">
             <div>
                 <h1 class="page-header-title">Members</h1>
                 <p class="page-header-sub">
                     {{ Auth::user()->isManager() ? 'Manage club members, roles, and teams' : 'View members and presence history' }}
                 </p>
             </div>
             <span class="chip chip--brand">
                 {{ $members->total() }} {{ Str::plural('member', $members->total()) }}
             </span>
         </div>
     </x-slot>

     <div class="content-wrap stack">

        {{-- Toast notifications (server flash + JS actions) --}}
        <div id="member-toasts" class="toast-stack" aria-live="polite"></div>
        @if (session('status'))
        <div class="alert alert-success animate-fade-in-up">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger animate-fade-in-up">{{ $errors->first() }}</div>
        @endif

         {{-- KPI strip --}}
         <div class="kpi-grid stagger">
             <div class="kpi-card">
                 <p class="stat-label">Total</p>
                 <p class="stat-value tabular mt-1">{{ $stats['total'] }}</p>
             </div>
             <div class="kpi-card">
                 <p class="stat-label">Active</p>
                 <p class="stat-value tabular mt-1" style="color: var(--teal-600);">{{ $stats['active'] }}</p>
             </div>
             <div class="kpi-card">
                 <p class="stat-label">Suspended</p>
                 <p class="stat-value tabular mt-1" style="color: var(--danger);">{{ $stats['suspended'] }}</p>
             </div>
             <div class="kpi-card">
                 <p class="stat-label">Deleted</p>
                 <p class="stat-value tabular mt-1" style="color: var(--gray-500);">{{ $stats['trashed'] }}</p>
             </div>
         </div>

         {{-- Filters --}}
         <form method="GET" action="{{ route('members.index') }}" class="card animate-fade-in-up">
             <div class="card-header">
                 <h2 class="card-title">Filters</h2>
             </div>

             <div class="card-body grid grid--2 gap-3" id="member-filters">
                 <div style="grid-column: span 1;">
                     <input
                         type="text"
                         name="q"
                         value="{{ request('q') }}"
                         placeholder="Search name, email, or CNE"
                         class="input">
                 </div>

                 <select name="team" class="field-select">
                     <option value="">All teams</option>
                     @foreach ($teams as $t)
                     <option value="{{ $t }}" @selected(request('team')===$t)>
                         {{ ucfirst($t) }}
                     </option>
                     @endforeach
                     <option value="none" @selected(request('team')==='none' )>No team</option>
                 </select>

                 <select name="role" class="field-select">
                     <option value="">All roles</option>
                     @foreach ($roles as $r)
                     <option value="{{ $r }}" @selected(request('role')===$r)>
                         {{ str_replace('_', ' ', $r) }}
                     </option>
                     @endforeach
                 </select>

                 <select name="filiere" class="field-select">
                     <option value="">All filières</option>
                     @foreach ($filieres as $f)
                     <option value="{{ $f }}" @selected(request('filiere')===$f)>
                         {{ $f }}
                     </option>
                     @endforeach
                 </select>

                 <select name="status" class="field-select">
                     <option value="">Any status</option>
                     <option value="active" @selected(request('status')==='active' )>Active only</option>
                     <option value="suspended" @selected(request('status')==='suspended' )>Suspended only</option>
                 </select>
             </div>

             <div class="card-footer flex-wrap">
                 <label class="flex items-center gap-2 text-sm cursor-pointer">
                     <input
                         type="checkbox"
                         name="trashed"
                         value="1"
                         @checked(request('trashed'))
                         class="checkbox">
                     Show deleted members
                 </label>

                 <div class="card-footer-actions">
                     @if (request()->hasAny(['q', 'team', 'role', 'filiere', 'status', 'trashed']))
                     <a href="{{ route('members.index') }}" class="text-action">Clear</a>
                     @endif

                     <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                 </div>
             </div>
         </form>

         {{-- Members table --}}
         <div class="card overflow-hidden animate-fade-in-up delay-100">
             @if ($members->isEmpty())
             <div class="empty-state">
                 <p class="empty-state-title">No matching members</p>
                 <p>Try adjusting or clearing your filters.</p>
             </div>
             @else
             <div class="table-wrap">
                 <table class="data-table">
                     <thead>
                         <tr>
                             <th>Member</th>
                             <th class="hide-mobile col-secondary">CNE</th>
                             <th class="only-desktop col-optional">Filière</th>
                             <th class="hide-mobile col-optional">Team</th>
                             <th class="hide-mobile">Roles</th>
                             <th class="td-right">Action</th>
                         </tr>
                     </thead>

                     <tbody>
                         @foreach ($members as $member)
                         <tr>
                             <td>
                                 <div class="person-cell">
                                     <span class="avatar">
                                         @if ($member->photo_path)
                                         <img src="{{ asset('storage/' . $member->photo_path) }}" alt="">
                                         @else
                                         {{ strtoupper(substr($member->name, 0, 1)) }}
                                         @endif
                                     </span>

                                     <div class="min-w-0">
                                         <div class="flex items-center gap-2 flex-wrap">
                                             <a href="{{ route('members.show', $member) }}" class="cell-title truncate">
                                                 {{ $member->name }}
                                             </a>

                                             @if ($member->trashed())
                                             <span class="chip" style="font-size: 10px; padding: 2px 8px;">Deleted</span>
                                             @elseif ($member->isSuspended())
                                             <span class="chip chip--danger" style="font-size: 10px; padding: 2px 8px;">Suspended</span>
                                             @endif
                                         </div>

                                         <p class="cell-sub truncate">{{ $member->email }}</p>
                                         <div class="mobile-member-chips hide-desktop" style="display: none;">
                                             @if ($member->team)
                                             <span class="chip chip--sm capitalize">{{ $member->team }}</span>
                                             @endif
                                             @foreach ($member->roles as $role)
                                             <span class="chip chip--sm {{ $role->name === 'president' ? 'chip--primary-solid' : '' }}">{{ str_replace('_', ' ', $role->name) }}</span>
                                             @endforeach
                                         </div>
                                     </div>
                                 </div>
                             </td>

                             <td class="hide-mobile col-secondary tabular">{{ $member->cne ?: '—' }}</td>
                             <td class="only-desktop col-optional">{{ $member->filiere ?: '—' }}</td>

                             <td class="hide-mobile col-optional">
                                 @if ($member->team)
                                 <span class="chip capitalize">{{ $member->team }}</span>
                                 @else
                                 <span class="text-muted">—</span>
                                 @endif
                             </td>

                             <td class="hide-mobile">
                                 <div class="chip-row">
                                     @forelse ($member->roles as $role)
                                     <span
                                         class="chip {{ $role->name === 'president' ? 'chip--primary-solid' : '' }}"
                                         style="font-size: 11px; padding: 2px 10px;">
                                         {{ str_replace('_', ' ', $role->name) }}
                                     </span>
                                     @empty
                                     <span class="text-muted text-xs">None</span>
                                     @endforelse
                                 </div>
                             </td>

                             <td class="td-right td-nowrap">
                                 <div class="app-actions">
                                     <a href="{{ route('members.show', $member) }}" class="text-action">View</a>

                                     @can('manage', $member)
                                     <button
                                         type="button"
                                         class="manage-trigger"
                                         data-manage-member
                                         data-name="{{ $member->name }}"
                                         data-edit-url="{{ route('members.edit', $member) }}"
                                         @can('updateRoles', $member)
                                         data-promote-url="{{ route('members.promote', $member) }}"
                                         data-is-president="{{ $member->hasRole('president') ? '1' : '0' }}"
                                         @endcan
                                         @if ($member->is_suspended)
                                         @can('unsuspend', $member)
                                         data-unsuspend-url="{{ route('members.unsuspend', $member) }}"
                                         @endcan
                                         @else
                                         @can('suspend', $member)
                                         data-suspend-url="{{ route('members.suspend', $member) }}"
                                         @endcan
                                         @endif
                                         @can('delete', $member)
                                         data-delete-url="{{ route('members.destroy', $member) }}"
                                         @endcan>
                                         Manage
                                     </button>
                                     @endcan

                                     @if ($member->trashed())
                                     @can('restore', $member)
                                     <form
                                         method="POST"
                                         action="{{ route('members.restore', $member) }}"
                                         class="inline-form"
                                         style="display: inline;">
                                         @csrf
                                         <button type="submit" class="btn btn-sm btn-secondary">Restore</button>
                                     </form>
                                     @endcan
                                     @endif
                                 </div>
                             </td>
                         </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
             @endif
         </div>

         {{-- Pagination --}}
         @if ($members->hasPages())
         <div>{{ $members->links() }}</div>
         @endif

     </div>

     <style>
         @media (min-width: 1024px) {
             #member-filters {
                 grid-template-columns: repeat(6, 1fr) !important;
             }

             #member-filters>div:first-child {
                 grid-column: span 2 !important;
             }
         }

         @media (min-width: 640px) and (max-width: 1023px) {
             #member-filters {
                 grid-template-columns: 1fr 1fr !important;
             }
         }
     </style>

     <script>
         (function () {
             function init() {
                 const TGD = window.TGD;
                 if (!TGD) return;

                 const csrf = TGD.csrf || document.querySelector('meta[name="csrf-token"]')?.content;

                 // Shared SVG icons matching the applications manage menu design
                 const svg = (path) => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + path + '</svg>';
                 const ICONS = {
                     edit: svg('<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>'),
                     promote: svg('<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>'),
                     demote: svg('<path d="M12 5v14M19 12l-7 7-7-7"/>'),
                     unsuspend: svg('<polygon points="5 3 19 12 5 21 5 3"/>'),
                     suspend: svg('<rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>'),
                     delete: svg('<path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13"/>'),
                 };

                 async function memberAction(url, method, body) {
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

                 document.addEventListener('click', async (e) => {
                     const manageBtn = e.target.closest('[data-manage-member]');
                     if (!manageBtn) return;
                     e.preventDefault();

                     if (manageBtn.disabled) return;
                     manageBtn.disabled = true;

                     const d = manageBtn.dataset;
                     const name = d.name || 'this member';
                     const isPresident = d.isPresident === '1';

                     const options = [];

                     if (d.editUrl) {
                         options.push({
                             key: 'edit',
                             label: 'Edit profile & team',
                             hint: 'Update personal details, team, and roles',
                             icon: ICONS.edit,
                         });
                     }

                     if (d.promoteUrl) {
                         if (isPresident) {
                             options.push({
                                 key: 'demote',
                                 label: 'Return to member role',
                                 hint: 'Remove president role from this member',
                                 icon: ICONS.demote,
                             });
                         } else {
                             options.push({
                                 key: 'promote',
                                 label: 'Promote to president',
                                 hint: 'Make this member the club president',
                                 icon: ICONS.promote,
                             });
                         }
                     }

                     if (d.unsuspendUrl) {
                         options.push({
                             key: 'unsuspend',
                             label: 'Reactivate account',
                             hint: 'Restore login access for this account',
                             icon: ICONS.unsuspend,
                         });
                     } else if (d.suspendUrl) {
                         options.push({
                             key: 'suspend',
                             label: 'Suspend account',
                             hint: 'Invalidate active sessions and block sign in',
                             icon: ICONS.suspend,
                             danger: true,
                         });
                     }

                     if (d.deleteUrl) {
                         options.push({
                             key: 'delete',
                             label: 'Delete member',
                             hint: 'Soft delete — can be restored later',
                             icon: ICONS.delete,
                             danger: true,
                         });
                     }

                     try {
                         const action = await TGD.menu({
                             title: 'Manage member',
                             subject: name,
                             subjectLabel: 'Member',
                             options,
                         });

                         if (!action) return;

                         if (action === 'edit') {
                             window.location.href = d.editUrl;
                             return;
                         }

                         if (action === 'promote') {
                             const confirmed = await TGD.confirm({
                                 title: 'Promote to president?',
                                 subject: name,
                                 message: `Make ${name} the club president? The current president will be demoted to member.`,
                                 confirmLabel: 'Promote to president',
                             });
                             if (!confirmed) return;

                             const data = await memberAction(d.promoteUrl, 'POST', { role: 'president' });
                             d.isPresident = '1';
                             const chipRow = manageBtn.closest('tr')?.querySelector('.chip-row');
                             if (chipRow) {
                                 chipRow.innerHTML = '<span class="chip chip--primary-solid" style="font-size:11px;padding:2px 10px;">president</span>';
                             }
                             TGD.toast(data.message || `${name} is now the club president.`, 'success');
                         } else if (action === 'demote') {
                             const confirmed = await TGD.confirm({
                                 title: 'Return to member role?',
                                 subject: name,
                                 message: `Return ${name} to the regular member role?`,
                                 confirmLabel: 'Return to member',
                             });
                             if (!confirmed) return;

                             const data = await memberAction(d.promoteUrl, 'POST', { role: 'member' });
                             d.isPresident = '0';
                             const chipRow = manageBtn.closest('tr')?.querySelector('.chip-row');
                             if (chipRow) {
                                 chipRow.innerHTML = '<span class="chip" style="font-size:11px;padding:2px 10px;">member</span>';
                             }
                             TGD.toast(data.message || `${name} returned to member role.`, 'success');
                         } else if (action === 'suspend') {
                             const confirmed = await TGD.confirm({
                                 title: 'Suspend account?',
                                 subject: name,
                                 message: `Suspend ${name}? Their active sessions will be invalidated immediately.`,
                                 confirmLabel: 'Suspend account',
                                 danger: true,
                             });
                             if (!confirmed) return;

                             const data = await memberAction(d.suspendUrl, 'POST');
                             TGD.toast(data.message || `${name} has been suspended.`, 'success');
                             setTimeout(() => window.location.reload(), 600);
                         } else if (action === 'unsuspend') {
                             const confirmed = await TGD.confirm({
                                 title: 'Reactivate account?',
                                 subject: name,
                                 message: `Reactivate ${name}'s account? They will be able to log in again.`,
                                 confirmLabel: 'Reactivate account',
                             });
                             if (!confirmed) return;

                             const data = await memberAction(d.unsuspendUrl, 'POST');
                             TGD.toast(data.message || `${name} has been reactivated.`, 'success');
                             setTimeout(() => window.location.reload(), 600);
                         } else if (action === 'delete') {
                             const confirmed = await TGD.confirm({
                                 title: 'Delete member?',
                                 subject: name,
                                 message: `Delete ${name}? This account can be restored later from the Deleted filter.`,
                                 confirmLabel: 'Delete member',
                                 danger: true,
                             });
                             if (!confirmed) return;

                             const data = await memberAction(d.deleteUrl, 'DELETE');
                             manageBtn.closest('tr')?.remove();
                             TGD.toast(data.message || `${name} has been deleted.`, 'success');
                         }
                     } catch (err) {
                         TGD.toast(err.message || 'Something went wrong.', 'error');
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