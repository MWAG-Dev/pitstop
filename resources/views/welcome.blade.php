<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pit Stop</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite([
            'resources/css/app.css',
            'resources/css/welcome_page.css',
        ])
    @else
        <style>
            /* keep the fallback tailwind blob you already had */
        </style>
    @endif
</head>

<body class="bg-[#FDFDFC] text-[#1b1b18] flex p-6 lg:p-8 min-h-screen flex-col">
    <div class="w-full max-w-3xl mx-auto">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="row">
                <img
                    src="{{ asset('images/pitstop-logo.png') }}"
                    alt="Pit Stop"
                    class="h-20 md:h-28 lg:h-32 w-auto mb-4"
                />
                <h1 class="section-title">Clear requests. Faster resolutions.</h1>
            </div>
        </div>

        {{-- GUEST VIEW --}}
        @guest
            <section class="guest-section">
                <div class="guest-card">
                    {{-- Decorative gradient background --}}
                    <div class="guest-bg-gradient"></div>
                    <div class="guest-orb guest-orb--top"></div>
                    <div class="guest-orb guest-orb--bottom"></div>

                    <div class="guest-inner">
                        <div class="guest-layout">
                            <div class="guest-content">
                                <div class="guest-badge">
                                    <span class="guest-badge-dot"></span>
                                    PitStop • Internal Support
                                </div>

                                <h2 class="guest-headline">
                                    Get help—without chasing people.
                                </h2>

                                <p class="guest-lede">
                                    Submit requests, track progress, and ensure no problem gets forgotten!
                                </p>

                                {{-- Actions --}}
                                <div class="guest-actions">
                                    <a href="{{ route('login') }}" class="button-log_in">
                                        Log in
                                    </a>

                                    <form action="{{ route('register') }}" method="GET">
                                        <button type="submit" class="button-create_account">
                                            Create account
                                        </button>
                                    </form>
                                </div>

                                <p class="guest-footnote">
                                    <b>Use your company email.</b> If you can’t access your account,
                                    <a href="mailto:dev@mwmotor.com" class="email-link">contact</a>
                                    the Operations team.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endguest
    </div>
</body>
</html>