<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pit Stop</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite([
            'resources/css/app.css',
            'resources/css/dashboard.css',
            'resources/js/app.js'
        ])
    @else
        <style>
            /* keep the fallback tailwind blob you already had */
        </style>
    @endif
</head>

<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <img src="{{ asset('images/pitstop-logo.png') }}" alt="PitStop" class="dash-logo" />
                <div class="brand-title">
                    <strong>Dashboard</strong>
                    <span>
                        {{ auth()->user()->email }}
                        @if(auth()->user()->isAdmin() || auth()->user()->role === 'dev')
                            — Role: {{ auth()->user()->role }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="topbar-actions" style="display:flex; gap:10px; align-items:center;">
                <a class="btn btn-primary" href="{{ route('tickets.create') }}">+ New Request</a>

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
        <div class="dash-grid">
            <section class="card">
                <div class="dash-toprow">
                    <div>
                        <h2 class="dash-h2">Overview</h2>
                        <div class="dash-meta">You are viewing requests associated with your company account.</div>
                    </div>
                    <div class="pill">Tip: click a recent request to open it.</div>
                </div>

                <div class="dash-stats">
                    <div class="dash-stat">
                        <div class="dash-stat-label">My requests</div>
                        <div class="dash-stat-value">{{ $myTicketsCount ?? 0 }}</div>
                    </div>

                    <div class="dash-stat">
                        <div class="dash-stat-label">My open requests</div>
                        <div class="dash-stat-value">{{ $myOpenCount ?? 0 }}</div>
                    </div>
                </div>
            </section>

            @php
                $myTicketsShowRoute = \Route::has('my_tickets.show')
                    ? 'my_tickets.show'
                    : (\Route::has('tickets.show') ? 'tickets.show' : null);
            @endphp

            <section class="card">
                <div class="dash-toprow">
                    <div>
                        <h2 class="dash-h2">My Recent Requests</h2>
                        <div class="dash-meta">Here are your most recent requests. Click a request to view more details.</div>
                    </div>
                </div>

                @if(!empty($myTickets) && $myTickets->count())
                    <ul class="dash-ticket-list">
                        @foreach($myTickets as $ticket)
                            <li class="dash-ticket-item">
                                <a href="{{ $myTicketsShowRoute ? route($myTicketsShowRoute, $ticket) : '#' }}" class="dash-ticket-link">
                                    <span class="dash-ticket-title">
                                        {{ $ticket->subject }}
                                        @if(!empty($unreadTicketIds) && in_array($ticket->id, $unreadTicketIds, true))
                                            <span class="notif-badge" aria-label="New reply" title="New reply">!</span>
                                        @endif
                                    </span>
                                    <span class="dash-ticket-status">{{ ucfirst($ticket->status) }}</span>
                                    <span class="dash-ticket-date">{{ $ticket->created_at->format('M j, Y') }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="dash-meta" style="margin-top: 10px;">No recent requests 🎉🎉</div>
                @endif

                <div class="dash-actions">
                    <a href="{{ \Route::has('my_tickets.index') ? route('my_tickets.index') : (\Route::has('tickets.index') ? route('tickets.index') : '#') }}" class="btn">
                        View All My Requests
                    </a>
                </div>
            </section>

            @if(!empty($opsSummary))
                <section class="card">
                    <div class="dash-toprow">
                        <div>
                            <h2 class="dash-h2">Ops Queue Summary</h2>
                            <div class="dash-meta">High-level request counts across the entire system.</div>
                        </div>
                    </div>

                    <div class="dash-opsgrid">
                        <div class="dash-stat">
                            <div class="dash-stat-label">Total</div>
                            <div class="dash-stat-value">{{ $opsSummary['total'] ?? 0 }}</div>
                        </div>

                        <div class="dash-stat">
                            <div class="dash-stat-label">Open</div>
                            <div class="dash-stat-value">{{ $opsSummary['open'] ?? 0 }}</div>
                        </div>

                        <div class="dash-stat">
                            <div class="dash-stat-label">In Progress</div>
                            <div class="dash-stat-value">{{ $opsSummary['in_progress'] ?? 0 }}</div>
                        </div>

                        <div class="dash-stat">
                            <div class="dash-stat-label">Waiting</div>
                            <div class="dash-stat-value">{{ $opsSummary['waiting'] ?? 0 }}</div>
                        </div>

                        <div class="dash-stat">
                            <div class="dash-stat-label">Resolved</div>
                            <div class="dash-stat-value">{{ $opsSummary['resolved'] ?? 0 }}</div>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </main>
</body>
</html>