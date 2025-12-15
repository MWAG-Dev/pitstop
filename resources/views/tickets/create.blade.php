<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit a Request</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; padding: 24px; max-width: 760px; margin: 0 auto; }
        label { display: block; margin-top: 14px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ddd; border-radius: 10px; }
        button { margin-top: 18px; padding: 10px 14px; border: 0; border-radius: 10px; cursor: pointer; }
        .success { background: #e8fff0; border: 1px solid #b7f0cc; padding: 12px; border-radius: 10px; margin-bottom: 14px; }
        .error { background: #fff1f1; border: 1px solid #f3b9b9; padding: 12px; border-radius: 10px; margin-top: 14px; }
        ul { margin: 0; padding-left: 18px; }
    </style>
</head>
<body>
    <h1>Submit an Operations Request</h1>
    <p>Use this form to report issues with software, hardware, phones, websites, access, or anything that feels “off.”</p>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tickets.store') }}">
        @csrf

        <label for="requester_email">Your Email</label>
        <input id="requester_email" name="requester_email" type="email" required value="{{ old('requester_email') }}" placeholder="name@company.com">

        <label for="category">Category</label>
        <select id="category" name="category" required>
            @php $cat = old('category', 'General'); @endphp
            @foreach(['General','Phone System','Website','CRM','Hardware','Access/Permissions','Network','Other'] as $c)
                <option value="{{ $c }}" @selected($cat === $c)>{{ $c }}</option>
            @endforeach
        </select>

        <label for="priority">Priority</label>
        <select id="priority" name="priority" required>
            @php $pri = old('priority', 'Normal'); @endphp
            @foreach(['Low','Normal','High','Critical'] as $p)
                <option value="{{ $p }}" @selected($pri === $p)>{{ $p }}</option>
            @endforeach
        </select>

        <label for="subject">Subject</label>
        <input id="subject" name="subject" type="text" required maxlength="150"
               value="{{ old('subject') }}" placeholder="Short summary (ex: Phones dropping calls at Logan)">

        <label for="description">Description</label>
        <textarea id="description" name="description" required rows="6" maxlength="5000"
                  placeholder="What happened? When did it start? Who is affected? Any error messages?">{{ old('description') }}</textarea>

        <button type="submit">Submit Ticket</button>
    </form>
</body>
</html>
