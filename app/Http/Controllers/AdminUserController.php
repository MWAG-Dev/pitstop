<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        $selectedUser = null;
        $selectedUserId = request()->query('user');

        if ($selectedUserId) {
            $selectedUser = User::find($selectedUserId);
        }

        return view('admin.users.index', [
            'users' => $users,
            'selectedUser' => $selectedUser,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:employee,ops,admin'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->password = $data['password'];
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:employee,ops,admin'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Updated {$user->email}.");
    }

    public function updatePassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = $data['password'];
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Password reset for {$user->email}.");
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Deleted {$user->email}.");
    }
}
