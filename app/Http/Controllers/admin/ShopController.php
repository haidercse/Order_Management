<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::query()->with(['users' => fn ($users) => $users->role('shop-manager')->orderBy('id')]);

        //search filter
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('manager_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

       //status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        //pagisnation
        $shops = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        //ajax request
        if ($request->ajax()) {

            return response()->json([
                'html' => view(
                    'backend.pages.shops.partials.table',
                    compact('shops')
                )->render()
            ]);
        }

        //normal page load
        return view(
            'backend.pages.shops.index',
            compact('shops')
        );
    }


    //store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:shops,code',
            'name' => 'required|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'manager_login_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'manager_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $managerPassword = filled($validated['manager_password'] ?? null)
            ? $validated['manager_password']
            : '12345678';

        $shop = DB::transaction(function () use ($validated, $managerPassword): Shop {
            $shop = Shop::create(collect($validated)->except(['manager_login_email', 'manager_password', 'manager_password_confirmation'])->all());
            $user = User::create([
                'name' => $shop->manager_name ?: $shop->name . ' Manager',
                'email' => $validated['manager_login_email'],
                'password' => $managerPassword,
                'shop_id' => $shop->id,
            ]);
            $user->assignRole(Role::findByName('shop-manager', 'web'));

            return $shop;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Shop and manager login created successfully. Share the credentials securely with the manager.',
            'credentials' => [
                'email' => $validated['manager_login_email'],
                'password' => $managerPassword,
            ],
        ]);
    }


   //edit
    public function edit($id)
    {
        $shop = Shop::with(['users' => fn ($users) => $users->role('shop-manager')->orderBy('id')])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                ...$shop->toArray(),
                'manager_login_email' => $shop->users->first()?->email,
            ],
        ]);
    }


    
    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:shops,code,' . $id,
            'name' => 'required|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'manager_login_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($shop->users()->role('shop-manager')->value('id'))],
            'manager_password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $managerPassword = filled($validated['manager_password'] ?? null)
            ? $validated['manager_password']
            : '12345678';
        $hasManager = $shop->users()->role('shop-manager')->exists();
        $credentialUpdate = !$hasManager || filled($validated['manager_password'] ?? null);

        DB::transaction(function () use ($shop, $validated, $managerPassword): void {
            $shop->update(collect($validated)->except(['manager_login_email', 'manager_password', 'manager_password_confirmation'])->all());
            $manager = $shop->users()->role('shop-manager')->lockForUpdate()->first();

            if (!$manager) {
                $manager = new User([
                    'name' => $shop->manager_name ?: $shop->name . ' Manager',
                    'email' => $validated['manager_login_email'],
                    'password' => $managerPassword,
                    'shop_id' => $shop->id,
                ]);
                $manager->save();
                $manager->assignRole(Role::findByName('shop-manager', 'web'));

                return;
            }

            $manager->name = $shop->manager_name ?: $shop->name . ' Manager';
            $manager->email = $validated['manager_login_email'];
            if (!empty($validated['manager_password'])) {
                $manager->password = $validated['manager_password'];
                $manager->must_change_password = false;
            }
            $manager->save();
        });

        $response = [
            'status' => 'success',
            'message' => 'Shop and manager account updated successfully.',
        ];

        if ($credentialUpdate) {
            $response['message'] = 'Shop updated. Share the manager login credentials securely.';
            $response['credentials'] = [
                'email' => $validated['manager_login_email'],
                'password' => $managerPassword,
            ];
        }

        return response()->json($response);
    }

    public function resetManagerPassword(int $id)
    {
        $shop = Shop::findOrFail($id);
        $manager = $shop->users()->role('shop-manager')->firstOrFail();
        $temporaryPassword = Str::random(18);

        $manager->forceFill([
            'password' => $temporaryPassword,
            'must_change_password' => true,
        ])->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Temporary password generated. Share it securely; the manager must change it at next login.',
            'credentials' => [
                'email' => $manager->email,
                'password' => $temporaryPassword,
            ],
        ]);
    }


    
    public function destroy($id)
    {
        $shop = Shop::findOrFail($id);

        $shop->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Shop deleted successfully!'
        ]);
    }
}