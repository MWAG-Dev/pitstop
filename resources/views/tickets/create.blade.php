<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</title>
    @vite(['resources/css/create.css'])
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
                    <strong>PitStop</strong>
                    <span>Operations Ticketing</span>
                </div>
            </div>

            <div class="topbar-actions">
                <a class="btn" href="{{ url('/') }}" aria-label="Go to Dashboard">
                    ← Dashboard
                </a>

                <span class="pill" title="Signed in user">
                    {{ auth()->user()->email }}
                </span>
            </div>
        </div>
    </header>

    <main class="shell page">
        <div class="header">
            <div>
                <h1>Submit an Operations Request</h1>
                <p class="sub">Report issues with software, hardware, phones, websites, access, or anything that feels off. The more detail you provide, the faster we can resolve it.</p>
            </div>

            <div class="pill" title="Tip">
                Tip: include screenshots or exact error text.
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following:</strong>
                <ul>
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="card" aria-label="Ticket submission form">
            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="card-inner">
                    <div class="grid grid-2">
                        <div>
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                @php $cat = old('category', 'General'); @endphp
                                @foreach(['General','Phone System','Website','CRM','Hardware','Access/Permissions','Network','Other'] as $c)
                                    <option value="{{ $c }}" @selected($cat === $c)>{{ $c }}</option>
                                @endforeach
                            </select>
                            <div class="hint">Pick the closest match. It helps route the request.</div>
                        </div>

                        <div>
                            <label for="priority">Priority</label>
                            <select id="priority" name="priority" required>
                                @php $pri = old('priority', 'Normal'); @endphp
                                @foreach(['Low','Normal','High','Critical'] as $p)
                                    <option value="{{ $p }}" @selected($pri === $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                            <div class="hint">Use <span class="kbd">Critical</span> only if work is blocked for multiple people.</div>
                        </div>
                    </div>

                    <div class="grid" style="margin-top: 14px;">
                        <div>
                            <label for="subject">Subject</label>
                            <input id="subject" name="subject" type="text" required maxlength="150"
                                   value="{{ old('subject') }}"
                                   placeholder="Short summary (ex: Phones dropping calls at Logan)">
                            <div class="hint">Keep it short and specific. Example: “VPN down at Logan”</div>
                        </div>

                        <div>
                            <label for="description">Description</label>
                            <textarea id="description" name="description" required rows="7" maxlength="5000"
                                      placeholder="What happened? When did it start? Who is affected? Any error messages?">{{ old('description') }}</textarea>
                            <div class="hint">Include steps to reproduce, device/browser, and any error codes/messages.</div>
                        </div>
  
                        <div>
                            <label for="attachments">Attachments</label>
                            <input
                                id="attachments"
                                name="attachments[]"
                                type="file"
                                multiple
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt"
                            >
                            <div class="hint">
                                Optional. Add screenshots, PDFs, or other files that help us troubleshoot.
                                (Recommended: keep each file under 10MB.)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <div class="meta">
                        Submitting as <strong style="color: rgba(255,255,255,0.90);">{{ auth()->user()->email }}</strong>
                    </div>

                    <button class="btn btn-primary" type="submit">Submit Ticket</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
