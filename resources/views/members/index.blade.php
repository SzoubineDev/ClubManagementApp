 <x-app-layout>
     <x-slot name="header">
         <div class="flex items-start justify-between gap-4 w-full flex-wrap">
             <div>
                 <h1 class="page-header-title">Members</h1>
                 <p class="page-header-sub">Manage club members, roles, and teams</p>
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
                             <th>Roles</th>
                             <th></th>
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

                             <td>
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

                             <td
                                 class="td-right td-nowrap"
                                 x-data="{ open: false }"
                                 @click.outside="open = false"
                                 @keydown.escape.window="open = false"
                                 style="position: relative;">
                                 <a href="{{ route('members.show', $member) }}" class="text-action">View</a>

                                 @can('manage', $member)
                                 <button
                                     type="button"
                                     class="manage-trigger ml-2"
                                     :aria-expanded="open"
                                     aria-haspopup="menu"
                                     @click="open = !open">
                                     Manage ▾
                                 </button>

                                 <div
                                     class="menu-panel nb-menu"
                                     x-cloak
                                     x-show="open"
                                     x-transition.opacity.duration.120ms
                                     role="menu"
                                     style="position: absolute; right: 0; top: calc(100% + 6px); z-index: 40;">
                                     <a href="{{ route('members.edit', $member) }}" role="menuitem">
                                         ✎ Edit profile &amp; team
                                     </a>

                                     @can('updateRoles', $member)
                                     <button type="button" role="menuitem" data-action="promote"
                                         data-url="{{ route('members.promote', $member) }}"
                                         data-role="{{ $member->hasRole('president') ? 'member' : 'president' }}"
                                         data-confirm="{{ $member->hasRole('president')
                                             ? 'Return ' . $member->name . ' to the regular member role?'
                                             : 'Make ' . $member->name . ' the club president? The current president will be demoted to member.' }}">
                                         {{ $member->hasRole('president') ? '↓ Return to member' : '★ Promote to president' }}
                                     </button>
                                     @endcan

                                     <hr>

                                     @if ($member->is_suspended)
                                     @can('unsuspend', $member)
                                     <button type="button" role="menuitem" data-action="post"
                                         data-url="{{ route('members.unsuspend', $member) }}"
                                         data-confirm="">▶ Reactivate account</button>
                                     @endcan
                                     @else
                                     @can('suspend', $member)
                                     <button type="button" role="menuitem" data-action="post"
                                         data-url="{{ route('members.suspend', $member) }}"
                                         data-confirm="Suspend {{ $member->name }}? Their existing sessions will be invalidated.">⏸ Suspend account</button>
                                     @endcan
                                     @endif

                                     @can('delete', $member)
                                     <button type="button" role="menuitem" class="nb-menu-danger" data-action="delete"
                                         data-url="{{ route('members.destroy', $member) }}"
                                         data-confirm="Delete {{ $member->name }}? This can be restored later from the Deleted filter.">✕ Delete member</button>
                                     @endcan
                                 </div>
                                 @endcan

                                 @if ($member->trashed())
                                 @can('restore', $member)
                                 <form
                                     method="POST"
                                     action="{{ route('members.restore', $member) }}"
                                     class="inline-form ml-2"
                                     style="display: inline;">
                                     @csrf
                                     <button type="submit" class="btn btn-sm btn-secondary">Restore</button>
                                 </form>
                                 @endcan
                                 @endif
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
             const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
             const stack = document.getElementById('member-toasts');

             function toast(message, type = 'success') {
                 if (!stack) return;
                 const el = document.createElement('div');
                 el.className = 'toast toast--' + type;
                 el.setAttribute('role', 'status');
                 el.textContent = message;
                 stack.appendChild(el);
                setTimeout(() => { el.classList.add('toast--out'); }, 3800);
                 setTimeout(() => el.remove(), 4300);
             }

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
                     throw new Error(data.message || data.errors
                         ? Object.values(data.errors || {})[0]?.[0] || data.message
                         : 'Request failed (' + res.status + ')');
                 }
                 return data;
             }

             document.addEventListener('click', async (e) => {
                 const btn = e.target.closest('[data-action]');
                 if (!btn) return;
                 e.preventDefault();

                 const { action, url, confirm: msg, role } = btn.dataset;
                 if (msg && !window.confirm(msg)) return;

                 const original = btn.textContent;
                 btn.disabled = true;
                 btn.textContent = 'Working…';

                 try {
                     let data;
                     if (action === 'promote') {
                         data = await memberAction(url, 'POST', { role });
                     } else if (action === 'delete') {
                         data = await memberAction(url, 'DELETE');
                     } else {
                         data = await memberAction(url, 'POST');
                     }

                     toast(data.message || 'Done.', 'success');

                     // Update UI in place where possible.
                     if (action === 'promote') {
                         btn.textContent = role === 'president'
                             ? '↓ Return to member'
                             : '★ Promote to president';
                         btn.dataset.role = role === 'president' ? 'member' : 'president';
                         btn.dataset.confirm = role === 'president'
                             ? 'Return this member to the regular member role?'
                             : 'Make this member the club president? The current president will be demoted to member.';
                         const chipRow = btn.closest('tr')?.querySelector('.chip-row');
                         if (chipRow) chipRow.innerHTML = '<span class="chip chip--primary-solid" style="font-size:11px;padding:2px 10px;">'
                             + (role === 'president' ? 'president' : 'member') + '</span>';
                     } else if (action === 'delete') {
                         btn.closest('tr')?.remove();
                     } else if (action === 'post') {
                         // Suspend/unsuspend: reload so status chips stay accurate.
                         window.location.reload();
                         return;
                     }
                 } catch (err) {
                     toast(err.message || 'Something went wrong.', 'error');
                 } finally {
                     btn.disabled = false;
                     if (btn.isConnected && action !== 'promote') btn.textContent = original;
                 }
             });
         })();
     </script>
 </x-app-layout>