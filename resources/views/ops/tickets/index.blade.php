<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ops Queue</title>

    @vite([
        'resources/css/app.css',
        'resources/css/ops.css',
        'resources/js/app.js'
    ])
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <img
                    src="{{ asset('images/pitstop-logo.png') }}"
                    alt="PitStop"
                    class="brand-logo"
                />
                <div class="brand-title">
                    <strong>Ops Queue</strong>
                    <span>System-wide request management</span>
                </div>
            </div>

            <div class="topbar-actions" style="display:flex; gap:10px; align-items:center;">
                <a class="btn" href="{{ url('/') }}">← Dashboard</a>
                <a class="btn btn-primary" href="{{ route('tickets.create') }}">+ Submit Request</a>

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button type="button" class="btn" style="display:flex; gap:6px; align-items:center;">
                            Menu
                            <svg class="fill-current" style="height:16px;width:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('my_tickets.index')">My Requests</x-dropdown-link>

                        @if(auth()->user()->isOps())
                            <x-dropdown-link :href="route('ops.tickets.index')">Ops Queue</x-dropdown-link>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <x-dropdown-link :href="route('admin.users.index')">User Management</x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('profile.edit')">Settings</x-dropdown-link>

                        <div class="border-t border-gray-100 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </header>

    <main class="shell page">
        <div class="ops-header">
            <div>
                <h1 class="ops-title">Ops Request Queue</h1>
                <div class="ops-sub">All submitted requests (priority first).</div>
            </div>
        </div>

        <section class="card" aria-label="Filters" style="margin-top: 14px;">
            <div style="padding: 14px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <label style="display:flex; flex-direction:column; gap:6px;">
                        <span class="ops-muted" style="font-size: 0.78rem;">Status</span>
                        <select id="opsFilterStatus" style="padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,0.14);background:rgba(7, 11, 20, 0.30);color:rgba(255,255,255,0.92);outline:none;">
                            <option value="">All (Open)</option>
                            <option value="open">Open</option>
                            <option value="in progress">In Progress</option>
                            <option value="waiting">Waiting</option>
                            <option value="closed">Closed</option>
                        </select>
                    </label>

                    <label style="display:flex; flex-direction:column; gap:6px;">
                        <span class="ops-muted" style="font-size: 0.78rem;">Priority</span>
                        <select id="opsFilterPriority" style="padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,0.14);background:rgba(7, 11, 20, 0.30);color:rgba(255,255,255,0.92);outline:none;">
                            <option value="">All</option>
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </label>

                    <label style="display:flex; flex-direction:column; gap:6px;">
                        <span class="ops-muted" style="font-size: 0.78rem;">Sort</span>
                        <select id="opsSort" style="padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,0.14);background:rgba(7, 11, 20, 0.30);color:rgba(255,255,255,0.92);outline:none;">
                            <option value="priority" selected>Priority</option>
                            <option value="newest">Newest</option>
                            <option value="oldest">Oldest</option>
                            <option value="status">Status</option>
                        </select>
                    </label>
                </div>

                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <span id="opsResultsCount" class="pill" aria-live="polite">Showing —</span>
                    <button type="button" id="opsReset" class="btn">Reset</button>
                </div>
            </div>
        </section>

        <section class="card" aria-label="Ops requests" style="margin-top: 14px;">
            <div class="ops-table-wrap">
                <table class="ops-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Category</th>
                            <th>Subject</th>
                            <th>Requester</th>
                            <th>Created</th>
                            @if(auth()->user()->isAdmin())
                                <th>Delete</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($tickets as $t)
                        <tr onclick="window.location='{{ route('ops.tickets.show', $t) }}'" style="cursor:pointer;" data-status="{{ strtolower($t->status) }}" data-priority="{{ strtolower($t->priority) }}" data-created="{{ optional($t->created_at)->timestamp ?? 0 }}">
                            <td>
                                <span class="ops-link">#{{ $t->id }}</span>
                                @if(!empty($unreadTicketIds) && in_array($t->id, $unreadTicketIds, true))
                                    <span class="notif-badge" aria-label="New reply" title="New reply">!</span>
                                @endif
                            </td>
                            <td><span class="ops-pill">{{ $t->status }}</span></td>
                            <td><span class="ops-pill">{{ $t->priority }}</span></td>
                            <td>{{ $t->category }}</td>
                            <td><span class="ops-link">{{ $t->subject }}</span></td>
                            <td class="ops-muted">{{ $t->requester_email }}</td>
                            <td class="ops-muted">{{ $t->created_at->format('Y-m-d g:i A') }}</td>
                            @if(auth()->user()->isAdmin())
                                <td>
                                    <form method="POST" action="{{ route('ops.tickets.destroy', $t) }}" onsubmit="return confirm('Delete request #{{ $t->id }}? This cannot be undone.');" onclick="event.stopPropagation();">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn" style="padding:6px 10px; border-color: rgba(255, 107, 107, 0.45); color: rgba(255, 107, 107, 0.95);" onclick="event.stopPropagation();">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="ops-muted">No requests yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
    (function () {
      const statusEl = document.getElementById('opsFilterStatus');
      const priorityEl = document.getElementById('opsFilterPriority');
      const sortEl = document.getElementById('opsSort');
      const resetEl = document.getElementById('opsReset');
      const countEl = document.getElementById('opsResultsCount');
    const statusParam = new URLSearchParams(window.location.search).get('status') || '';

      const tbody = document.querySelector('.ops-table tbody');
      if (!tbody) return;

      const priorityRank = { critical: 4, high: 3, normal: 2, low: 1 };
      const statusRank = { open: 4, 'in progress': 3, waiting: 2, resolved: 1, closed: 0 };

            if (statusEl) {
                statusEl.value = statusParam;
                statusEl.addEventListener('change', () => {
                    const value = (statusEl.value || '').trim();
                    const params = new URLSearchParams(window.location.search);
                    if (value) {
                        params.set('status', value);
                    } else {
                        params.delete('status');
                    }
                    window.location.search = params.toString();
                });
            }

      function rows() {
        return Array.from(tbody.querySelectorAll('tr')).filter(r => r.querySelector('td'));
      }

      function apply() {
        const priority = (priorityEl?.value || '').trim();
        const sort = (sortEl?.value || 'newest').trim();

        const list = rows();

        // Filter
        list.forEach(r => {
          const rPriority = (r.dataset.priority || '').trim();

          const matchPriority = !priority || rPriority === priority;
          r.style.display = matchPriority ? '' : 'none';
        });

        // Sort (only visible rows)
        const visible = list.filter(r => r.style.display !== 'none');

        visible.sort((a, b) => {
          const aCreated = parseInt(a.dataset.created || '0', 10);
          const bCreated = parseInt(b.dataset.created || '0', 10);

          const aPri = priorityRank[(a.dataset.priority || '').trim()] ?? 0;
          const bPri = priorityRank[(b.dataset.priority || '').trim()] ?? 0;

          const aStat = statusRank[(a.dataset.status || '').trim()] ?? 0;
          const bStat = statusRank[(b.dataset.status || '').trim()] ?? 0;

          if (sort === 'newest') return bCreated - aCreated;
          if (sort === 'oldest') return aCreated - bCreated;
          if (sort === 'priority') return bPri - aPri;
          if (sort === 'status') return bStat - aStat;
          return 0;
        });

        visible.forEach(r => tbody.appendChild(r));

        const total = list.length;
        const shown = visible.length;
        if (countEl) countEl.textContent = `Showing ${shown} of ${total}`;
      }

            function reset() {
                if (statusEl) statusEl.value = '';
                if (priorityEl) priorityEl.value = '';
                if (sortEl) sortEl.value = 'priority';
                const params = new URLSearchParams(window.location.search);
                params.delete('status');
                window.location.search = params.toString();
                return;
            }

            [priorityEl, sortEl].forEach(el => el && el.addEventListener('change', apply));
      resetEl && resetEl.addEventListener('click', reset);

      apply();
    })();
    </script>
</body>
</html>
