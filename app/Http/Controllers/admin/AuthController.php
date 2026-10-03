<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login()
    {
        return view('backend.pages.auth.login');
    }

    public function loginAll(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $identifier = trim($data['login']);
        $user = User::query()->where('email', $identifier)->first();

        if (!$user) {
            $shops = Shop::query()
                ->where('status', 'active')
                ->where('name', $identifier)
                ->get();

            if ($shops->count() > 1) {
                throw ValidationException::withMessages([
                    'login' => 'More than one shop has this name. Please contact the administrator.',
                ]);
            }

            if ($shop = $shops->first()) {
                $managers = User::query()
                    ->role('shop-manager')
                    ->where('shop_id', $shop->id)
                    ->get();

                if ($managers->count() > 1) {
                    throw ValidationException::withMessages([
                        'login' => 'This shop has multiple manager accounts. Please contact the administrator.',
                    ]);
                }

                $user = $managers->first();
            }
        }

        $remember = $request->filled('remember');
        if ($user && Auth::attempt(['email' => $user->email, 'password' => $data['password']], $remember)) {
            $authenticatedUser = Auth::user();

            if (!$authenticatedUser->hasAnyRole(['shop-manager', 'super-admin', 'warehouse-manager'])) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'login' => 'This account does not have access to the ordering system.',
                ]);
            }

            $request->session()->regenerate();

            if ($authenticatedUser->hasRole('shop-manager')) {
                return redirect()->route('shop.orders.index');
            }

            return redirect()->route('admin.dashboard');
        }

        throw ValidationException::withMessages([
            'login' => 'Invalid shop name/email or password.',
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
