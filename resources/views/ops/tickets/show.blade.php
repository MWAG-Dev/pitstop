<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket #{{ $ticket->id }}</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; padding: 24px; max-width: 900px; margin: 0 auto; }
        .row { display:flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
        .pill { display:inline-block; padding: 4px 10px; border-radius: 999px; border: 1px solid #ddd; font-size: 12px; }
        .muted { color:#666; font-size: 13px; }
        .card { border: 1px solid #eee; border-radius: 14px; padding: 14px; margin-top: 14px; }
        textarea { width: 100%; }
        select, button { padding: 10px; border-radius: 10px; border: 1px solid #ddd; }
        button { cursor: pointer; }
        a { color: inherit; }
        .success { background: #e8fff0; border: 1px solid #b7f0cc; padding: 12px; border-radius: 10px; margin-top: 14px; }
    </style>
</head>
<body>
    <div class="row">
        <div>
            <h1>Ticket #{{ $ticket->id }}</h1>
            <div class="muted">
                Submitted {{ $ticket->created_at->format('Y-m-d g:i A') }} by {{ $ticket->requester_email }}
            </div>
        </div>
        <div>
            <a href="{{ route('ops.tickets.index') }}">← Back to queue</a>
        </div>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="row">
            <div>
                <div><strong>Subject:</strong> {{ $ticket->subject }}</div>
                <div class="muted" style="margin-top:6px;">
                    <span class="pill">{{ $ticket->status }}</span>
                    <span class="pill">{{ $ticket->priority }}</span>
                    <span class="pill">{{ $ticket->category }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('ops.tickets.status', $ticket) }}">
                @csrf
                <label class="muted" for="status">Update Status</label><br>
                <select id="status" name="status" required>
                    @foreach(['Open','In Progress','Waiting','Closed'] as $s)
                        <option value="{{ $s }}" @selected($ticket->status === $s)>{{ $s }}</option>
                    @endforeach
                </select>
                <button type="submit">Save</button>
            </form>
        </div>

        <hr style="border:none;border-top:1px solid #eee;margin:14px 0;">

        <div>
            <strong>Description</strong>
            <div style="margin-top:8px; white-space: pre-wrap;">{{ $ticket->description }}</div>
        </div>

<div class="card">
    <h3 style="margin-top:0;">Replies</h3>

    @forelse($ticket->replies as $reply)
        <div style="padding:12px 0; border-top:1px solid #eee;">
            <div class="muted" style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                <div>
                    <span class="pill">{{ strtoupper($reply->author_role) }}</span>
                    @if($reply->author_email)
                        <span class="muted">{{ $reply->author_email }}</span>
                    @endif
                </div>
                <div class="muted">{{ $reply->created_at->format('Y-m-d g:i A') }}</div>
            </div>

            <div style="margin-top:8px; white-space:pre-wrap;">{{ $reply->message }}</div>
        </div>
    @empty
        <div class="muted">No replies yet.</div>
    @endforelse
</div>

<div class="card">
    <h3 style="margin-top:0;">Post a Reply</h3>

    <form method="POST" action="{{ route('ops.tickets.reply', $ticket) }}">
        @csrf

        <label class="muted">Your Email (optional)</label>
        <input type="email" name="author_email"
               style="width:100%; padding:10px; margin-top:6px; border:1px solid #ddd; border-radius:10px;">

        <label class="muted" style="display:block; margin-top:12px;">Message</label>
        <textarea name="message" rows="5" required
                  style="width:100%; padding:10px; margin-top:6px; border:1px solid #ddd; border-radius:10px;"></textarea>

        <button type="submit" style="margin-top:12px;">Send Reply</button>
    </form>
</div>

    </div>
</body>
</html>
