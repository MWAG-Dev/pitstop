<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket #{{ $ticket->id }}</title>

    @vite([
        'resources/css/app.css',
        'resources/css/ticket.css',
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
                    <strong>Ticket #{{ $ticket->id }}</strong>
                    <span>Ops Ticket Detail</span>
                </div>
            </div>

            <div class="topbar-actions">
                <a class="btn" href="{{ route('ops.tickets.index') }}">← Back to queue</a>
                <a class="btn" href="{{ url('/') }}">Dashboard</a>
                <a class="btn btn-primary" href="{{ route('tickets.create') }}">+ Submit Ticket</a>
            </div>
        </div>
    </header>

    <main class="shell page">
        <div class="ticket-head">
            <div>
                <h1 class="ticket-title">Ticket #{{ $ticket->id }}</h1>
                <div class="ticket-sub">
                    Submitted {{ $ticket->created_at->format('Y-m-d g:i A') }} by {{ $ticket->requester_email }}
                </div>

                <div class="ticket-meta" aria-label="Ticket tags">
                    <span class="ticket-pill">{{ $ticket->status }}</span>
                    <span class="ticket-pill">{{ $ticket->priority }}</span>
                    <span class="ticket-pill">{{ $ticket->category }}</span>
                </div>
            </div>

            <div class="pill">Tip: update status on the right.</div>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="ticket-grid" style="margin-top: 14px;">
            <section class="card" aria-label="Ticket details">
                <div style="padding: 16px 18px;">
                    <h2 class="ticket-section-title">Subject</h2>
                    <div class="ticket-body">{{ $ticket->subject }}</div>

                    <div style="height: 14px;"></div>

                    <h2 class="ticket-section-title">Description</h2>
                    <div class="ticket-body">{{ $ticket->description }}</div>
                </div>
            </section>

            @if(auth()->check() && auth()->user()->role === 'admin')
                <section class="card" aria-label="Update status">
                    <div style="padding: 16px 18px;">
                        <h2 class="ticket-section-title">Update Status</h2>

                        <form method="POST" action="{{ route('ops.tickets.status', $ticket) }}">
                            @csrf

                            <div class="field">
                                <label class="label" for="status">Status</label>
                                <select id="status" name="status" class="select" required>
                                    @foreach(['Open','In Progress','Waiting','Closed'] as $s)
                                        <option value="{{ $s }}" @selected($ticket->status === $s)>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </section>
            @else
                <section class="card" aria-label="Status permissions">
                    <div style="padding: 16px 18px;">
                        <h2 class="ticket-section-title">Update Status</h2>
                        <div class="ticket-sub">Only admins can update ticket status.</div>
                    </div>
                </section>
            @endif
        </div>

        <section class="card" aria-label="Replies" style="margin-top: 14px;">
            <div style="padding: 16px 18px;">
                <h2 class="ticket-section-title">Replies</h2>

                <div class="replies">
                    @forelse($ticket->replies as $reply)
                        <div class="reply">
                            <div class="reply-head">
                                <div class="reply-author">
                                    <span class="ticket-pill">{{ strtolower($reply->author_role) }}</span>
                                    @if($reply->author_email)
                                        <span>{{ $reply->author_email }}</span>
                                    @endif
                                </div>
                                <div>{{ $reply->created_at->format('Y-m-d g:i A') }}</div>
                            </div>

                            <div class="reply-body">{{ $reply->message }}</div>
                        </div>
                    @empty
                        <div class="ticket-sub" style="margin-top: 10px;">No replies yet.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="card" aria-label="Post a reply" style="margin-top: 14px;">
            <div style="padding: 16px 18px;">
                <h2 class="ticket-section-title">Post a Reply</h2>

                <form method="POST" action="{{ route('ops.tickets.reply', $ticket) }}">
                    @csrf

                    <div class="field">
                        <label class="label" for="author_email">Your Email (optional)</label>
                        <input id="author_email" class="input" type="email" name="author_email" autocomplete="email">
                    </div>

                    <div class="field">
                        <label class="label" for="message">Message</label>
                        <textarea id="message" class="textarea" name="message" rows="5" required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Send Reply</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>