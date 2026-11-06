<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with(['roles' => fn ($query) => $query->where('guard_name', 'web')->where('name', '!=', 'shop-manager')])
            ->whereHas('roles', fn ($query) => $query->where('guard_name', 'web'))
            ->whereDoesntHave('roles', fn ($query) => $query->where('guard_name', 'web')->where('name', 'shop-manager'))
            ->latest('id')
            ->get();

        if (request()->ajax()) {
            return response()->json([
                'html' => view('backend.pages.users.partials.table', compact('users'))->render(),
            ]);
        }

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->where('name', '!=', 'shop-manager')
            ->orderBy('name')
            ->get();

        return view('backend.pages.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web')
                    ->whereNotIn('name', ['shop-manager']),
            ],
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $user->assignRole(Role::findByName($validated['role'], 'web'));
        });

        if ($request->ajax()) {
            return response()->json(['message' => 'User created successfully.']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $user = $this->managedUsers()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web')
                    ->whereNotIn('name', ['shop-manager']),
            ],
        ]);

        DB::transaction(function () use ($user, $validated): void {
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (! empty($validated['password'])) {
                $user->password = $validated['password'];
            }

            $user->save();
            $user->syncRoles([$validated['role']]);
        });

        return response()->json(['message' => 'User updated successfully.']);
    }

    public function destroy(int $id)
    {
        $user = $this->managedUsers()->findOrFail($id);

        abort_if($user->is(auth()->user()), 422, 'You cannot delete your own account.');
        abort_if($user->hasRole('super-admin'), 422, 'Super admin accounts cannot be deleted.');

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    private function managedUsers()
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->where('guard_name', 'web'))
            ->whereDoesntHave('roles', fn ($query) => $query->where('guard_name', 'web')->where('name', 'shop-manager'));
    }
}
