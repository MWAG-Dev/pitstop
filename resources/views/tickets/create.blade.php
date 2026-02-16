<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Request</title>
    @vite([
        'resources/css/app.css',
        'resources/css/create.css',
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
                    <strong>PitStop</strong>
                    <span>Operations Requests</span>
                </div>
            </div>

            <div class="topbar-actions" style="display:flex; gap:10px; align-items:center;">
                <a class="btn" href="{{ url('/') }}" aria-label="Go to Dashboard">
                    ← Dashboard
                </a>

                <span class="pill" title="Signed in user">
                    {{ auth()->user()->email }}
                </span>

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button type="button" class="btn" style="display:flex; gap:6px; align-items:center;">
                            Menu
                            <svg class="fill-current" style="height:16px;width:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('my_tickets.index')">My Requests</x-dropdown-link>

                        @if(auth()->user()->isOps())
                            <x-dropdown-link :href="route('ops.tickets.index')">Ops Queue</x-dropdown-link>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <x-dropdown-link :href="route('admin.users.index')">User Management</x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('profile.edit')">Settings</x-dropdown-link>

                        <div class="border-t border-gray-100 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
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

        <section class="card" aria-label="Request submission form">
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

                    <button class="btn btn-primary" type="submit">Submit Request</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
