<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings</title>

    @vite([
        'resources/css/app.css',
        'resources/css/settings.css',
        'resources/js/app.js'
    ])
</head>
<body class="settings-shell">
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="Go to Dashboard">
                <img src="{{ asset('images/pitstop-logo.png') }}" alt="PitStop" class="brand-logo" />
                <div class="brand-title">
                    <strong>Settings</strong>
                    <span>Profile & security</span>
                </div>
            </a>

            <div class="topbar-actions" style="display:flex; gap:10px; align-items:center;">
                <a class="btn" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="btn btn-primary" href="{{ route('tickets.create') }}">+ New Request</a>

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
        <div class="header-row">
            <div>
                <h1 class="page-title">Settings</h1>
                <p class="page-sub">To update your profile reach out to the dev team. You can update your password below.</p>
            </div>
            <div class="pill">Signed in as {{ auth()->user()->email }}</div>
        </div>

        <div class="settings-stack">
            <section class="card" aria-label="Profile information">
                <div class="card-inner">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </section>

            <section class="card" aria-label="Update password">
                <div class="card-inner">
                    @include('profile.partials.update-password-form')
                </div>
            </section>

        </div>
    </main>
</body>
</html>
