<h2>New Ops Ticket Submitted</h2>

<p><strong>Ticket:</strong> #{{ $ticket->id }}</p>
<p><strong>From:</strong> {{ $ticket->requester_email }}</p>
<p><strong>Category:</strong> {{ $ticket->category }}</p>
<p><strong>Priority:</strong> {{ $ticket->priority }}</p>
<p><strong>Status:</strong> {{ $ticket->status }}</p>

<p><strong>Subject:</strong> {{ $ticket->subject }}</p>

<p><strong>Description:</strong></p>
<div style="white-space: pre-wrap;">{{ $ticket->description }}</div>

<hr>
<p>View:</p>
<p><a href="{{ url('/ops/tickets/' . $ticket->id) }}">{{ url('/ops/tickets/' . $ticket->id) }}</a></p>