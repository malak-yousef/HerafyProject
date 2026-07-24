<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function showLogin()
    {
        return response()->view('cms.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'يرجى ادخال البريد الالكتروني',
            'email.email'       => 'يرجى ادخال بريد الكتروني صحيح',
            'password.required' => 'يرجى ادخال كلمة المرور',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->status != 'active') {
                return response()->json([
                    'icon' => 'error',
                    'title' => 'حسابك غير مفعل'
                ], 403);
            }

            if ($user->isCraftman()) {
                Auth::guard('craftman')->login($user, $request->filled('remember'));
            } elseif ($user->isCustomer()) {
                Auth::guard('customer')->login($user, $request->filled('remember'));
            } else {
                Auth::guard('admin')->login($user, $request->filled('remember'));
            }
            $request->session()->regenerate();
            return response()->json([
                'icon' => 'success',
                'title' => 'تم تسجيل الدخول بنجاح',
                'redirect' => url('cms/admin')
            ], 200);
        }
        return response()->json([
            'icon' => 'error',
            'title' => 'البريد الالكتروني او كلمة المرور غير صحيحة',
        ], 400);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::guard('customer')->check()) {
            Auth::guard('customer')->logout();
        } elseif (Auth::guard('craftman')->check()) {
            Auth::guard('craftman')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
