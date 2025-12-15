<h2>Update on your Ops Ticket</h2>

<p><strong>Ticket:</strong> #{{ $ticket->id }}</p>
<p><strong>Subject:</strong> {{ $ticket->subject }}</p>
<p><strong>Status:</strong> {{ $ticket->status }}</p>

<p><strong>Ops Reply:</strong></p>
<div style="white-space: pre-wrap;">{{ $reply->message }}</div>

<hr>
<p>Ticket link:</p>
<p><a href="{{ url('/ops/tickets/' . $ticket->id) }}">{{ url('/ops/tickets/' . $ticket->id) }}</a></p>