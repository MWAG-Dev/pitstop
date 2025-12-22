<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Tickets</title>

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
                    <strong>My Tickets</strong>
                    <span>{{ $email }}</span>
                </div>
            </div>

            <div class="topbar-actions">
                <a class="btn" href="{{ url('/') }}" aria-label="Go to Dashboard">← Dashboard</a>
                <a class="btn btn-primary" href="{{ route('tickets.create') }}">+ New Ticket</a>
            </div>
        </div>
    </header>

    <main class="shell page">
        <div class="header-row">
            <div>
                <h1 class="page-title">My Tickets</h1>
                <p class="page-sub">You are viewing tickets associated with your company account.</p>
            </div>

            <div class="pill" title="Signed in user">
                Signed in as: <strong style="color: rgba(255,255,255,0.90);">{{ $email }}</strong>
            </div>
        </div>

        <div class="tickets-grid" style="margin-top: 14px;">
            @forelse($tickets as $ticket)
                <section
                    class="ticket-card"
                    aria-label="Ticket {{ $ticket->id }}"
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
                                    <span class="ticket-id">#{{ $ticket->id }}</span>
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

                                <div class="badges" aria-label="Ticket tags">
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
                <p class="empty">No tickets yet.</p>
            @endforelse

            <div class="pagination-wrap">
                {{ $tickets->links() }}
            </div>
        </div>
    </main>
</body>
</html>