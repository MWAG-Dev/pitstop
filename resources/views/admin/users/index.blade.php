<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management</title>

    @vite([
        'resources/css/app.css',
        'resources/css/admin.css',
        'resources/js/app.js'
    ])
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="Go to Dashboard">
                <img src="{{ asset('images/pitstop-logo.png') }}" alt="PitStop" class="brand-logo" />
                <div class="brand-title">
                    <strong>User Management</strong>
                    <span>Admin tools for accounts</span>
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

                        <x-dropdown-link :href="route('admin.users.index')">User Management</x-dropdown-link>

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
                <h1 class="page-title">User Management</h1>
                <p class="page-sub">Create users and reset passwords from one place.</p>
            </div>
            <div class="pill">Admin only</div>
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="admin-grid">
            <section class="card" aria-label="Add user">
                <div class="card-inner">
                    <h2 class="section-title">Add user</h2>
                    <p class="section-sub">Invite a teammate and set their role.</p>

                    <form method="POST" action="{{ route('admin.users.store') }}" autocomplete="off">
                        @csrf

                        <div class="field">
                            <label for="name" class="label">Name</label>
                            <input id="name" name="name" type="text" class="input" value="{{ old('name') }}" placeholder="Luke Skywalker" autocomplete="off" required>
                            @if($errors->has('name'))
                                <div class="text-danger">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                        <div class="field">
                            <label for="email" class="label">Email</label>
                            <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" placeholder="luke.skywalker@example.com" autocomplete="off" autocapitalize="none" required>
                            @if($errors->has('email'))
                                <div class="text-danger">{{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        <div class="field">
                            <label for="role" class="label">Role</label>
                            <select id="role" name="role" class="select" required>
                                @foreach(['employee', 'ops', 'admin'] as $role)
                                    <option value="{{ $role }}" @selected(old('role', 'employee') === $role)>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('role'))
                                <div class="text-danger">{{ $errors->first('role') }}</div>
                            @endif
                        </div>

                        <div class="field">
                            <label for="password" class="label">Password</label>
                            <div class="password-field">
                                <input id="password" name="password" type="password" class="input" placeholder="Minimum 8 characters" autocomplete="new-password" required minlength="8">
                                <button type="button" class="password-toggle" aria-label="Show password" title="Show password">
                                    <svg class="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg class="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.81 21.81 0 0 1 5.06-6.94" />
                                        <path d="M1 1l22 22" />
                                        <path d="M9.9 4.24A9.77 9.77 0 0 1 12 4c7 0 11 8 11 8a21.56 21.56 0 0 1-4.87 6.32" />
                                        <path d="M14.12 14.12a3 3 0 0 1-4.24-4.24" />
                                    </svg>
                                </button>
                            </div>
                            @if($errors->has('password'))
                                <div class="text-danger">{{ $errors->first('password') }}</div>
                            @endif
                        </div>

                        <div class="field">
                            <label for="password_confirmation" class="label">Confirm password</label>
                            <div class="password-field">
                                <input id="password_confirmation" name="password_confirmation" type="password" class="input" placeholder="Re-enter password" autocomplete="new-password" required minlength="8">
                                <button type="button" class="password-toggle" aria-label="Show password" title="Show password">
                                    <svg class="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg class="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.81 21.81 0 0 1 5.06-6.94" />
                                        <path d="M1 1l22 22" />
                                        <path d="M9.9 4.24A9.77 9.77 0 0 1 12 4c7 0 11 8 11 8a21.56 21.56 0 0 1-4.87 6.32" />
                                        <path d="M14.12 14.12a3 3 0 0 1-4.24-4.24" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-actions" style="margin-top: 14px;">
                            <button type="submit" class="btn btn-primary">Create user</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="card" aria-label="Existing users">
                <div class="card-inner">
                    @if($selectedUser)
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                            <div>
                                <h2 class="section-title">Edit user</h2>
                                <p class="section-sub">Update profile details, reset password, or delete the account.</p>
                            </div>
                            <a class="btn" href="{{ route('admin.users.index') }}">← Back</a>
                        </div>

                        <form method="POST" action="{{ route('admin.users.update', $selectedUser) }}" style="margin-top: 12px;">
                            @csrf
                            @method('PATCH')

                            <div class="field">
                                <label for="edit_name" class="label">Name</label>
                                <input id="edit_name" name="name" type="text" class="input" value="{{ old('name', $selectedUser->name) }}" required>
                            </div>

                            <div class="field">
                                <label for="edit_email" class="label">Email</label>
                                <input id="edit_email" name="email" type="email" class="input" value="{{ old('email', $selectedUser->email) }}" required>
                            </div>

                            <div class="field">
                                <label for="edit_role" class="label">Role</label>
                                <select id="edit_role" name="role" class="select" required>
                                    @foreach(['employee', 'ops', 'admin'] as $role)
                                        <option value="{{ $role }}" @selected(old('role', $selectedUser->role) === $role)>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-actions" style="margin-top: 14px;">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>

                        <div style="margin-top: 18px;">
                            <h3 class="section-title" style="font-size: 0.98rem;">Reset password</h3>
                            <form method="POST" action="{{ route('admin.users.password', $selectedUser) }}" class="inline-form" style="margin-top: 10px;">
                                @csrf
                                @method('PATCH')
                                <div class="password-field">
                                    <input
                                        type="password"
                                        name="password"
                                        placeholder="New password"
                                        class="input"
                                        required
                                        minlength="8"
                                    />
                                    <button type="button" class="password-toggle" aria-label="Show password" title="Show password">
                                        <svg class="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg class="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.81 21.81 0 0 1 5.06-6.94" />
                                            <path d="M1 1l22 22" />
                                            <path d="M9.9 4.24A9.77 9.77 0 0 1 12 4c7 0 11 8 11 8a21.56 21.56 0 0 1-4.87 6.32" />
                                            <path d="M14.12 14.12a3 3 0 0 1-4.24-4.24" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="password-field">
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        placeholder="Confirm"
                                        class="input"
                                        required
                                        minlength="8"
                                    />
                                    <button type="button" class="password-toggle" aria-label="Show password" title="Show password">
                                        <svg class="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg class="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.81 21.81 0 0 1 5.06-6.94" />
                                            <path d="M1 1l22 22" />
                                            <path d="M9.9 4.24A9.77 9.77 0 0 1 12 4c7 0 11 8 11 8a21.56 21.56 0 0 1-4.87 6.32" />
                                            <path d="M14.12 14.12a3 3 0 0 1-4.24-4.24" />
                                        </svg>
                                    </button>
                                </div>
                                <button type="submit" class="btn">Reset</button>
                            </form>
                        </div>

                        <div style="margin-top: 18px;">
                            <h3 class="section-title" style="font-size: 0.98rem;">Delete user</h3>
                            <p class="section-sub">This action is permanent.</p>
                            <form method="POST" action="{{ route('admin.users.destroy', $selectedUser) }}" onsubmit="return confirm('Delete {{ $selectedUser->email }}? This cannot be undone.');" style="margin-top: 10px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="border-color: rgba(255, 107, 107, 0.45); color: rgba(255, 107, 107, 0.95);">
                                    Delete user
                                </button>
                            </form>
                        </div>
                    @else
                        <h2 class="section-title">Existing users</h2>
                        <p class="section-sub">Select a user to view details.</p>

                        <div class="table-wrap" style="margin-top: 12px;">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td class="section-sub">{{ $user->email }}</td>
                                            <td class="section-sub">{{ ucfirst($user->role) }}</td>
                                            <td>
                                                <a class="btn" href="{{ route('admin.users.index', ['user' => $user->id]) }}">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="text-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </main>
    <script>
        document.querySelectorAll('.password-toggle').forEach((button) => {
            button.addEventListener('click', () => {
                const field = button.closest('.password-field');
                const input = field ? field.querySelector('input') : null;
                if (!input) return;

                const isVisible = input.type === 'text';
                input.type = isVisible ? 'password' : 'text';
                button.classList.toggle('is-visible', !isVisible);
                const label = isVisible ? 'Show password' : 'Hide password';
                button.setAttribute('aria-label', label);
                button.setAttribute('title', label);
            });
        });
    </script>
</body>
</html>
