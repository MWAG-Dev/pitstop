@php
	$recipientType = $recipientType ?? 'requester';
	$isOpsRecipient = $recipientType === 'ops';
@endphp

<h2>{{ $isOpsRecipient ? 'New reply on an Ops Request' : 'Update on your Ops Request' }}</h2>

<p><strong>Request:</strong> #{{ $ticket->id }}</p>
<p><strong>Subject:</strong> {{ $ticket->subject }}</p>
<p><strong>Status:</strong> {{ $ticket->status }}</p>

<p><strong>{{ $isOpsRecipient ? 'Requester Reply:' : 'Ops Reply:' }}</strong></p>
<div style="white-space: pre-wrap;">{{ $reply->message }}</div>

<hr>
<p>Request link:</p>
@if($isOpsRecipient)
	<p><a href="{{ url('/ops/tickets/' . $ticket->id) }}">{{ url('/ops/tickets/' . $ticket->id) }}</a></p>
@else
	<p><a href="{{ url('/my-tickets/' . $ticket->id) }}">{{ url('/my-tickets/' . $ticket->id) }}</a></p>
@endif