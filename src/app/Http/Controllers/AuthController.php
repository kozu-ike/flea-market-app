<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Mail\VerifyEmail;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');  // ログインフォームを表示
    }
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('profile.setup');
        }
        return view('auth.register');  // 登録フォームを表示

    }
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        return redirect('/mypage/profile');
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (Auth::attempt($validated)) {
            return redirect('/mypage')->with('success', 'ログインしました');
        }

        return back()->withErrors(['email' => ' ログイン情報が登録されていません。']);
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'ログアウトしました');
    }

    public function sendVerificationEmail(User $user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id]
        );

        Mail::to($user->email)->send(new VerifyEmail($verificationUrl));
    }

    public function verifyEmail($id)
    {
        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->email_verified_at = now();
            $user->save();
        }

        return redirect('/mypage')->with('message', 'メール認証が完了しました');
    }
}
