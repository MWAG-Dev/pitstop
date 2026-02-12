<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Requests</title>

    @vite([
        'resources/css/app.css',
        'resources/css/my_tickets.css',
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
                    <strong>My Requests</strong>
                    <span>{{ $email }}</span>
                </div>
            </div>

            <div class="topbar-actions" style="display:flex; gap:10px; align-items:center;">
                <a class="btn" href="{{ url('/') }}" aria-label="Go to Dashboard">← Dashboard</a>
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
        <div class="header-row">
            <div>
                <h1 class="page-title">My Requests</h1>
                <p class="page-sub">You are viewing requests associated with your company account.</p>
            </div>

            <div class="pill" title="Signed in user">
                Signed in as: <strong style="color: rgba(255,255,255,0.90);">{{ $email }}</strong>
            </div>
        </div>

        @php
            // Tabs: default view hides closed/resolved; Closed tab shows only closed/resolved.
            $view = strtolower((string) request('view', 'active'));
            $view = $view === '' ? 'active' : $view;

            // Treat these as "closed" states.
            $closedStatuses = ['closed', 'resolved'];

            $tabHref = function (string $key) {
                // Keep URL clean for default tab.
                return $key === 'active'
                    ? route('my_tickets.index')
                    : route('my_tickets.index', ['view' => $key]);
            };

            $tabClass = function (string $key) use ($view) {
                return $view === $key ? 'is-active' : '';
            };
        @endphp

        <nav class="ticket-tabs" aria-label="Request filters">
            <a class="ticket-tab {{ $tabClass('active') }}" href="{{ $tabHref('active') }}">All (Active)</a>
            <a class="ticket-tab {{ $tabClass('closed') }}" href="{{ $tabHref('closed') }}">Closed</a>
        </nav>

        <div class="tickets-grid" style="margin-top: 14px;">
            @forelse($tickets as $ticket)
                @php
                    $ticketStatusNorm = strtolower((string) $ticket->status);

                    // Filter behavior:
                    // - active: exclude closed/resolved
                    // - closed: include only closed/resolved
                    $shouldRender = $view === 'closed'
                        ? in_array($ticketStatusNorm, $closedStatuses, true)
                        : !in_array($ticketStatusNorm, $closedStatuses, true);
                @endphp

                @if(!$shouldRender)
                    @continue
                @endif
                <section
                    class="ticket-card"
                    aria-label="Request {{ $ticket->id }}"
                    role="link"
                    tabindex="0"
                    onclick="window.location='{{ route('my_tickets.show', $ticket) }}'"
                    onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.location='{{ route('my_tickets.show', $ticket) }}'; }"
                    style="cursor:pointer;"
                >
                    <div class="ticket-card__inner">
                        <div class="ticket-top">
                            <div>
                                <div>
                                    <span class="ticket-id">
                                        #{{ $ticket->id }}
                                        @if(!empty($unreadTicketIds) && in_array($ticket->id, $unreadTicketIds, true))
                                            <span class="notif-badge" aria-label="New reply" title="New reply">!</span>
                                        @endif
                                    </span>
                                    <span aria-hidden="true">—</span>
                                    <span class="ticket-subject" style="text-decoration: underline; text-decoration-color: rgba(255,255,255,0.25);">{{ $ticket->subject }}</span>
                                </div>

                                <div class="ticket-meta">
                                    {{ $ticket->category }}
                                    <span aria-hidden="true">•</span>
                                    {{ $ticket->priority }}
                                    <span aria-hidden="true">•</span>
                                    Status: <strong class="badge badge--status">{{ $ticket->status }}</strong>
                                </div>

                                <div class="badges" aria-label="Request tags">
                                    <span class="badge">{{ $ticket->category }}</span>
                                    <span class="badge">{{ $ticket->priority }}</span>
                                    <span class="badge badge--status">{{ $ticket->status }}</span>
                                </div>
                            </div>

                            <div class="ticket-time">
                                {{ optional($ticket->created_at)->format('Y-m-d g:i A') }}
                            </div>
                        </div>

                        <div class="ticket-desc">{{ $ticket->description }}</div>

                        <div class="replies">
                            <p class="replies-title">Replies</p>

                            @forelse($ticket->replies as $reply)
                                <div class="reply">
                                    <div class="reply-head">
                                        <span>{{ strtoupper($reply->author_role) }} @if($reply->author_email) ({{ $reply->author_email }}) @endif</span>
                                        <span>{{ optional($reply->created_at)->format('Y-m-d g:i A') }}</span>
                                    </div>
                                    <div class="reply-body">{{ $reply->message }}</div>
                                </div>
                            @empty
                                <div class="empty" style="margin-top: 8px;">No replies yet.</div>
                            @endforelse
                        </div>
                    </div>
                </section>
            @empty
                <p class="empty">No requests yet.</p>
            @endforelse

            <div class="pagination-wrap">
                {{ $tickets->appends(request()->query())->links() }}
            </div>
        </div>
    </main>
</body>
</html>