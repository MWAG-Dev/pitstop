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
                    <span>{{ auth()->user()->email }} — Role: {{ auth()->user()->role }}</span>
                </div>
            </div>

            <div class="topbar-actions">
                <a class="btn" href="{{ route('tickets.create') }}">+ New Ticket</a>
                <a class="btn" href="{{ route('my_tickets.index') }}">My Tickets</a>

                @if(auth()->user()->isOps())
                    <a class="btn" href="{{ route('ops.tickets.index') }}">Ops Queue</a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn">Log out</button>
                </form>
            </div>
        </div>
    </header>

    <main class="shell page">
        <div class="dash-grid">
            <section class="card">
                <div class="dash-toprow">
                    <div>
                        <h2 class="dash-h2">Overview</h2>
                        <div class="dash-meta">You are viewing tickets associated with your company account.</div>
                    </div>
                    <div class="pill">Tip: click a recent ticket to open it.</div>
                </div>

                <div class="dash-stats">
                    <div class="dash-stat">
                        <div class="dash-stat-label">My tickets</div>
                        <div class="dash-stat-value">{{ $myTicketsCount ?? 0 }}</div>
                    </div>

                    <div class="dash-stat">
                        <div class="dash-stat-label">My open tickets</div>
                        <div class="dash-stat-value">{{ $myOpenCount ?? 0 }}</div>
                    </div>
                </div>
            </section>

            @if(!empty($myTickets) && $myTickets->count())
                @php
                    $myTicketsShowRoute = \Route::has('my_tickets.show')
                        ? 'my_tickets.show'
                        : (\Route::has('tickets.show') ? 'tickets.show' : null);
                @endphp

                <section class="card">
                    <div class="dash-toprow">
                        <div>
                            <h2 class="dash-h2">My Recent Tickets</h2>
                            <div class="dash-meta">Here are your most recent requests. Click a ticket to view more details.</div>
                        </div>
                    </div>

                    <ul class="dash-ticket-list">
                        @foreach($myTickets as $ticket)
                            <li class="dash-ticket-item">
                                <a href="{{ $myTicketsShowRoute ? route($myTicketsShowRoute, $ticket) : '#' }}" class="dash-ticket-link">
                                    <span class="dash-ticket-title">{{ $ticket->subject }}</span>
                                    <span class="dash-ticket-status">{{ ucfirst($ticket->status) }}</span>
                                    <span class="dash-ticket-date">{{ $ticket->created_at->format('M j, Y') }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="dash-actions">
                        <a href="{{ \Route::has('my_tickets.index') ? route('my_tickets.index') : (\Route::has('tickets.index') ? route('tickets.index') : '#') }}" class="btn">
                            View All My Tickets
                        </a>
                    </div>
                </section>
            @endif

            @if(!empty($opsSummary))
                <section class="card">
                    <div class="dash-toprow">
                        <div>
                            <h2 class="dash-h2">Ops Queue Summary</h2>
                            <div class="dash-meta">High-level ticket counts across the entire system.</div>
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