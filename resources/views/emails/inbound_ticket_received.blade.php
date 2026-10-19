<h2>We received your request</h2>

<p><strong>Request #:</strong> {{ $ticket->id }}</p>
<p><strong>Subject:</strong> {{ $ticket->subject }}</p>
<p><strong>Status:</strong> {{ $ticket->status }}</p>

@if($temporaryPassword)
<p>Your PitStop account has been created automatically.</p>
<p><strong>Username:</strong> {{ $user->email }}</p>
<p><strong>Temporary password:</strong> {{ $temporaryPassword }}</p>
<p>Please sign in and update your password immediately.</p>
@endif

<p>Sign in: <a href="{{ route('login') }}">{{ route('login') }}</a></p>
<p>Track your request: <a href="{{ url('/my-tickets/' . $ticket->id) }}">{{ url('/my-tickets/' . $ticket->id) }}</a></p>
