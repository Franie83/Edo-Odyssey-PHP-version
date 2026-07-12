<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Event;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Notification;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard.index');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $remember = $request->boolean('remember');
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();
            Auth::user()->update(['last_login_at' => now()]);
            Helpers::logAction('login', 'User', Auth::id(), 'User logged in');
            return redirect()->intended(route('dashboard.index'))->with('success', 'Welcome back, ' . Auth::user()->first_name . '!');
        }

        return back()->with('danger', 'Invalid email or password.');
    }

    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('dashboard.index');
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name'  => 'required|string|max:80',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6|confirmed',
            'role'       => 'required|in:Tourist,Guide,Hotel,Restaurant,Event Organizer',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'phone'      => $request->phone,
            'role'       => $request->role,
        ]);

        // ─── Auto‑create pending offering based on role ───
        switch ($user->role) {
            case 'Guide':
                Guide::create([
                    'user_id'             => $user->id,
                    'bio'                 => null,
                    'languages'           => null,
                    'experience'          => 0,
                    'hourly_rate'         => 0,
                    'verification_status' => 'Pending',
                ]);
                break;

            case 'Hotel':
                Hotel::create([
                    'user_id'         => $user->id,
                    'name'            => 'My Hotel', // placeholder, user will edit later
                    'description'     => null,
                    'is_featured'     => false,
                    'is_active'       => false,
                    'price_per_night' => 0,
                ]);
                break;

            case 'Restaurant':
                Restaurant::create([
                    'user_id'     => $user->id,
                    'name'        => 'My Restaurant',
                    'description' => null,
                    'is_featured' => false,
                    'is_active'   => false,
                ]);
                break;

            case 'Event Organizer':
                Event::create([
                    'user_id'     => $user->id,
                    'name'        => 'My Event',
                    'description' => null,
                    'is_featured' => false,
                    'is_active'   => false,
                ]);
                break;

            default:
                // Tourist or other roles – no auto‑creation
                break;
        }

        // Welcome notification
        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Welcome to Edo Odyssey!',
            'message' => 'Your account has been created. Explore Edo State and earn Heritage Points.',
            'type'    => 'info',
        ]);

        Auth::login($user);
        Helpers::logAction('register', 'User', $user->id, "New {$user->role} registered");

        return redirect()->route('dashboard.index')->with('success', 'Welcome to Edo Odyssey, ' . $user->first_name . '!');
    }

    public function logout(Request $request)
    {
        Helpers::logAction('logout', 'User', Auth::id(), 'User logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('main.home')->with('success', 'You have been logged out.');
    }

    /**
     * Demo quick-login for prototyping / local development only.
     * TEMPORARILY ENABLED FOR TESTING - Disable in production!
     */
    public function quickLogin(string $role)
    {
        // TEMPORARILY COMMENTED OUT FOR TESTING
        // if (app()->isProduction()) {
        //     abort(403, 'Quick-login is disabled in production.');
        // }

        $roleMap = [
            'superadmin' => ['email' => 'superadmin@edoodyssey.ng', 'role' => 'Super Admin', 'fn' => 'Frank', 'ln' => 'Egbeobawaye'],
            'admin'      => ['email' => 'admin@edoodyssey.ng',      'role' => 'Agency Admin', 'fn' => 'Comfort', 'ln' => 'Obi'],
            'tourist'    => ['email' => 'tourist@edoodyssey.ng',     'role' => 'Tourist',      'fn' => 'Akenzua', 'ln' => 'Musa'],
            'guide'      => ['email' => 'guide@edoodyssey.ng',       'role' => 'Guide',         'fn' => 'Osaro',   'ln' => 'Edokpayi'],
            'hotel'      => ['email' => 'hotel@edoodyssey.ng',       'role' => 'Hotel',         'fn' => 'Patience', 'ln' => 'Osagie'],
            'restaurant' => ['email' => 'restaurant@edoodyssey.ng',  'role' => 'Restaurant',    'fn' => 'Blessing', 'ln' => 'Uwagboe'],
        ];

        $data = $roleMap[strtolower($role)] ?? null;
        if (!$data) return redirect()->route('main.home')->with('danger', 'Unknown demo role.');

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name'     => $data['fn'],
                'last_name'      => $data['ln'],
                'password'       => Hash::make('demo1234'),
                'role'           => $data['role'],
                'email_verified' => true,
            ]
        );

        // Create guide profile if needed
        if ($user->role === 'Guide' && !$user->guide) {
            Guide::create([
                'user_id'             => $user->id,
                'bio'                 => 'Expert guide in Edo State heritage sites.',
                'languages'           => 'English, Yoruba, Bini',
                'experience'          => 8,
                'hourly_rate'         => 5000,
                'daily_rate'          => 30000,
                'verification_status' => 'Approved',
            ]);
        }

        Auth::login($user, true);
        $user->update(['last_login_at' => now()]);
        Helpers::logAction('quick_login', 'User', $user->id, "Quick login as {$data['role']}");

        return redirect()->route('dashboard.index')->with('success', 'Logged in as ' . $user->full_name . ' (' . $user->role . ')');
    }
}