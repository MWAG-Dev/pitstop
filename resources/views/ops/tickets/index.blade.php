<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ops Queue</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; padding: 24px; max-width: 1100px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #eee; vertical-align: top; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: #444; }
        a { color: inherit; }
        .pill { display:inline-block; padding: 4px 10px; border-radius: 999px; border: 1px solid #ddd; font-size: 12px; }
        .muted { color:#666; font-size: 13px; }
        .topbar { display:flex; justify-content: space-between; align-items: center; gap: 16px; }
        .btn { display:inline-block; padding: 10px 12px; border-radius: 10px; border: 1px solid #ddd; text-decoration: none; }
    </style>
</head>
<body>
    <div class="topbar">
        <div>
            <h1>Ops Ticket Queue</h1>
            <div class="muted">All submitted requests (newest first).</div>
        </div>
        <a class="btn" href="{{ route('tickets.create') }}">+ Submit Ticket</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Category</th>
                <th>Subject</th>
                <th>Requester</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
        @forelse($tickets as $t)
            <tr>
                <td><a href="{{ route('ops.tickets.show', $t) }}">#{{ $t->id }}</a></td>
                <td><span class="pill">{{ $t->status }}</span></td>
                <td><span class="pill">{{ $t->priority }}</span></td>
                <td>{{ $t->category }}</td>
                <td><a href="{{ route('ops.tickets.show', $t) }}">{{ $t->subject }}</a></td>
                <td class="muted">{{ $t->requester_email }}</td>
                <td class="muted">{{ $t->created_at->format('Y-m-d g:i A') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="muted">No tickets yet.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
