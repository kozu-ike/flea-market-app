<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
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
        // 署名付きURLの生成
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify', // ルート名
            now()->addMinutes(60),  // リンクの有効期限（60分）
            ['id' => $user->id]     // パラメータ
        );

        // メール送信
        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));
    }


    // メール認証処理
    public function verifyEmail($id, $hash)
    {
        $user = User::findOrFail($id);

        // リンクのハッシュ値が一致するか確認
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'このリンクは無効です。');
        }

        // まだ認証されていなければ認証を行う
        if (!$user->hasVerifiedEmail()) {
            $user->email_verified_at = now();
            $user->save();
        }

        // 認証完了後、マイページへリダイレクト
        return redirect('/mypage')->with('message', 'メール認証が完了しました');
    }
}
