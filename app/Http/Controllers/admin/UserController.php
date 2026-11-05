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
        $users = User::with('roles')->latest('id')->paginate(15);
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
                Rule::exists('roles', 'name')->where('guard_name', 'web'),
                Rule::notIn(['shop-manager']),
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

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }
}
