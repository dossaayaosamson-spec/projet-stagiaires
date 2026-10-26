<?php

namespace App\Http\Controllers;

use App\Mail\SendOtpCode;
use App\Mail\WelcomeEmail;
use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        $cacheDuration = 1440;

        $booksCount = Cache::remember('welcome_books_count', $cacheDuration, function () {
            return Book::count();
        });

        $readersCount = Cache::remember('welcome_readers_count', $cacheDuration, function () {
            return User::count();
        });

        return view('auth.login', compact('booksCount', 'readersCount'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
            ])->onlyInput('email');
        }

        $code = $user->generateOtpCode();

        try {
            Mail::to($user->email)->send(new SendOtpCode($code, $user->prenom));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email OTP: '.$e->getMessage());
        }

        session([
            '2fa_user_id' => $user->id,
            '2fa_remember' => $request->boolean('remember'),
        ]);

        return redirect()->route('login.otp');
    }

    public function showOtp()
    {
        if (! session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session()->get('2fa_user_id'));

        if (! $user) {
            session()->forget(['2fa_user_id', '2fa_remember']);

            return redirect()->route('login');
        }

        $emailParts = explode('@', $user->email);
        $maskedName = substr($emailParts[0], 0, 2).str_repeat('*', max(1, strlen($emailParts[0]) - 2));
        $maskedEmail = $maskedName.'@'.($emailParts[1] ?? 'domain.com');

        return view('auth.otp', compact('maskedEmail'));
    }

    public function verifyOtp(Request $request)
    {
        if (! session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::find(session()->get('2fa_user_id'));

        if (! $user || ! $user->verifyOtpCode($request->otp_code)) {
            return back()->withErrors([
                'otp_code' => 'Code de vérification invalide ou expiré.',
            ]);
        }

        $user->clearOtpCode();
        $remember = session('2fa_remember', false);
        session()->forget(['2fa_user_id', '2fa_remember']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended('dashboard');
    }

    public function resendOtp()
    {
        if (! session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session()->get('2fa_user_id'));

        if ($user) {
            $code = $user->generateOtpCode();
            try {
                Mail::to($user->email)->send(new SendOtpCode($code, $user->prenom));
            } catch (\Exception $e) {
                Log::error('Erreur renvoi email OTP: '.$e->getMessage());
            }
        }

        return back()->with('success', 'Un nouveau code de vérification vous a été envoyé par e-mail.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'prenom' => ['required', 'string', 'max:255'],
            'nom_famille' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'prenom' => $request->prenom,
            'nom_famille' => $request->nom_famille,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'pays' => $request->country ?? 'tg',
            'newsletter' => $request->has('newsletter'),
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            Log::error('Welcome email failed: '.$e->getMessage());
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
