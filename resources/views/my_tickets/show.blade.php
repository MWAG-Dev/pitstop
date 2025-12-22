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
                <div class="brand-mark" aria-hidden="true"></div>
                <div class="brand-title">
                    <strong>Ticket #{{ $ticket->id }}</strong>
                    <span>My Ticket</span>
                </div>
            </div>

            <div class="topbar-actions">
                <a class="btn" href="{{ route('my_tickets.index') }}">← My Tickets</a>
                <a class="btn" href="{{ url('/') }}">Dashboard</a>
                <a class="btn btn-primary" href="{{ route('tickets.create') }}">+ New Ticket</a>
            </div>
        </div>
    </header>

    <main class="shell page">
        <div class="ticket-head">
            <div>
                <h1 class="ticket-title">Ticket #{{ $ticket->id }}</h1>
                <div class="ticket-sub">Submitted {{ $ticket->created_at->format('M j, Y \a\t g:i A') }}</div>

                <div class="ticket-meta" aria-label="Ticket tags">
                    <span class="ticket-pill">{{ $ticket->status }}</span>
                    <span class="ticket-pill">{{ $ticket->priority }}</span>
                    <span class="ticket-pill">{{ $ticket->category }}</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <section class="card" aria-label="Errors" style="margin-top: 14px;">
                <div style="padding: 16px 18px;">
                    <h2 class="ticket-section-title">Please fix the following</h2>
                    <div class="ticket-sub" style="margin-top: 10px;">
                        <ul style="margin:0; padding-left: 18px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
        @endif

        <section class="card" aria-label="Ticket details" style="margin-top: 14px;">
            <div style="padding: 16px 18px;">
                <h2 class="ticket-section-title">Subject</h2>
                <div class="ticket-body">{{ $ticket->subject }}</div>

                <div style="height: 14px;"></div>

                <h2 class="ticket-section-title">Description</h2>
                <div class="ticket-body">{{ $ticket->description }}</div>
            </div>
        </section>

        <section class="card" aria-label="Conversation" style="margin-top: 14px;">
            <div style="padding: 16px 18px;">
                <h2 class="ticket-section-title">Conversation</h2>

                <div class="replies">
                    @forelse($ticket->replies as $reply)
                        <div class="reply">
                            <div class="reply-head">
                                <div class="reply-author">
                                    <span class="ticket-pill">
                                        {{ $reply->author_role === 'ops' ? 'ops' : 'requester' }}
                                    </span>
                                    @if($reply->author_email)
                                        <span>{{ $reply->author_email }}</span>
                                    @endif
                                </div>
                                <div>{{ $reply->created_at->format('M j, Y g:i A') }}</div>
                            </div>

                            <div class="reply-body">{{ $reply->message }}</div>
                        </div>
                    @empty
                        <div class="ticket-sub" style="margin-top: 10px;">No replies yet.</div>
                    @endforelse
                </div>
            </div>
        </section>

        @php
            $replyAction = \Route::has('my_tickets.reply')
                ? route('my_tickets.reply', $ticket)
                : (\Route::has('tickets.reply') ? route('tickets.reply', $ticket) : null);
        @endphp

        <section class="card" aria-label="Post a reply" style="margin-top: 14px;">
            <div style="padding: 16px 18px;">
                <h2 class="ticket-section-title">Post a Reply</h2>
                <div class="ticket-sub" style="margin-top: 6px;">Ask a question, add details, or respond to Ops. This will be added to the ticket conversation.</div>

                @if($replyAction)
                    <form method="POST" action="{{ $replyAction }}" style="margin-top: 12px;" enctype="multipart/form-data">
                        @csrf

                        <div class="field">
                            <label class="label" for="message">Message</label>
                            <textarea id="message" class="textarea" name="message" rows="5" required>{{ old('message') }}</textarea>
                        </div>

                        <div class="field" style="margin-top: 12px;">
                            <label class="label" for="attachments">Attachments</label>
                            <input
                                id="attachments"
                                name="attachments[]"
                                type="file"
                                multiple
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt"
                            >
                            <div class="ticket-sub" style="margin-top: 6px; font-size: 12px;">
                                Optional. Add screenshots, PDFs, or other files to support your reply (up to ~10MB each).
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Send Reply</button>
                        </div>
                    </form>
                @else
                    <div class="ticket-sub" style="margin-top: 10px;">Reply route is not configured yet. Add a POST route named <strong>my_tickets.reply</strong> (or <strong>tickets.reply</strong>) to enable replies from users.</div>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
