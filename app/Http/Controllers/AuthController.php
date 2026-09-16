<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return response()->view('cms.auth.login');
    }

    public function showRegister(Request $request)
    {
        $professions = Profession::where('status', 'active')->get();
        $googleRegistration = $request->session()->get('google_registration');
        $googleNameParts = preg_split('/\s+/', trim((string) ($googleRegistration['name'] ?? '')), 2);
        $googleFirstName = $googleNameParts[0] ?? '';
        $googleLastName = $googleNameParts[1] ?? '';

        return response()->view('cms.auth.register', compact(
            'professions',
            'googleRegistration',
            'googleFirstName',
            'googleLastName'
        ));
    }

    public function redirectToGoogle()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->withErrors([
                'google' => 'تسجيل الدخول عبر Google غير متاح حالياً.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = mb_strtolower(trim((string) $googleUser->getEmail()));
            $googleId = trim((string) $googleUser->getId());

            if ($email === '' || $googleId === '') {
                throw new \RuntimeException('Google did not return the required identity fields.');
            }

            $user = User::where('email', $email)->first();

            if ($user && $this->hasPermanentlyDeletedActorProfile($user)
                && $user->google_id && hash_equals($user->google_id, $googleId)) {
                $user->forceDelete();
                $user = null;
            }

            if ($user) {
                if ($this->hasDeletedActorProfile($user)) {
                    return redirect()->route('login')->withErrors([
                        'google' => 'هذا الحساب محذوف. اطلب من الإدارة استعادته من سلة المحذوفات ثم حاول مجددًا.',
                    ]);
                }

                if ($user->status !== 'active') {
                    return redirect()->route('login')->withErrors(['google' => 'حسابك غير مفعل.']);
                }

                if ($user->google_id && ! hash_equals($user->google_id, $googleId)) {
                    return redirect()->route('login')->withErrors([
                        'google' => 'تعذر ربط حساب Google بهذا البريد.',
                    ]);
                }

                if (! $user->google_id) {
                    $user->forceFill([
                        'google_id' => $googleId,
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ])->save();
                }

                $redirect = $this->loginUser($user);

                if (! $redirect) {
                    return redirect()->route('login')->withErrors([
                        'google' => 'هذا الحساب غير مرتبط بنوع مستخدم صالح.',
                    ]);
                }

                $request->session()->regenerate();

                return redirect($redirect)->with('success', 'تم تسجيل الدخول عبر Google بنجاح.');
            }

            $request->session()->put('google_registration', [
                'id' => $googleId,
                'email' => $email,
                'name' => trim((string) $googleUser->getName()),
                'started_at' => now()->timestamp,
            ]);

            return redirect()->route('register')->with(
                'status',
                'تم التحقق من بريد Google. أكمل البيانات لإنشاء حسابك.'
            );
        } catch (\Throwable $exception) {
            Log::warning('Google authentication failed.', [
                'exception' => $exception,
            ]);

            return redirect()->route('login')->withErrors([
                'google' => 'تعذر تسجيل الدخول عبر Google. يرجى المحاولة مرة أخرى.',
            ]);
        }
    }

    public function showForgotPassword()
    {
        return view('cms.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            PasswordBroker::broker()->sendResetLink($request->only('email'));
        } catch (\Throwable $exception) {
            Log::error('Password reset link delivery failed.', [
                'exception' => $exception,
            ]);
        }

        return back()->with('status', 'إذا كان البريد مسجلاً، فسيتم إرسال رابط إعادة تعيين كلمة المرور إليه.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('cms.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            $status = PasswordBroker::broker()->reset(
                $validated,
                function (User $user, string $password): void {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );
        } catch (\Throwable $exception) {
            Log::error('Password reset failed.', ['exception' => $exception]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'تعذر تغيير كلمة المرور حالياً. يرجى المحاولة لاحقاً.']);
        }

        if ($status === PasswordBroker::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'تم تغيير كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function register(Request $request)
    {
        $googleRegistration = $request->session()->get('google_registration');
        $isGoogleRegistration = is_array($googleRegistration)
            && isset($googleRegistration['id'], $googleRegistration['email'], $googleRegistration['started_at'])
            && now()->timestamp - (int) $googleRegistration['started_at'] <= 900;

        if ($isGoogleRegistration) {
            $request->merge([
                'email' => $googleRegistration['email'],
                'name' => trim((string) ($googleRegistration['name'] ?: $request->input('name'))),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|max:45|unique:users,user_name',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => $isGoogleRegistration
                ? ['nullable']
                : ['required', 'confirmed', Password::min(8)],
            'mobile' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date' => 'nullable|date',
            'account_type' => 'required|in:user,craftsman',
            // حقول الحرفي
            'id_number' => 'required_if:account_type,craftsman|string|regex:/^\d+$/|max:255|unique:craftmen,id_number',
            'profession_id' => [
                'required_if:account_type,craftsman',
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    if ($request->account_type !== 'craftsman' || $value === 'other') {
                        return;
                    }

                    if (! Profession::whereKey($value)->where('status', 'active')->exists()) {
                        $fail('الحرفة المحددة غير صالحة.');
                    }
                },
            ],
            'experience_years' => 'required_if:account_type,craftsman|integer|min:0|max:255',
            'hourly_rate' => 'nullable|integer|min:0',
            'daily_rate' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:2000',
            // حرفة مخصصة (أخرى)
            'custom_craft' => 'required_if:profession_id,other|nullable|string|min:2|max:255',
        ], [
            'name.required' => 'يرجى ادخال الاسم الكامل',
            'user_name.required' => 'يرجى ادخال اسم المستخدم',
            'user_name.unique' => 'اسم المستخدم مستخدم مسبقاً',
            'email.required' => 'يرجى ادخال البريد الالكتروني',
            'email.email' => 'يرجى ادخال بريد الكتروني صحيح',
            'email.unique' => 'البريد الالكتروني مستخدم مسبقاً',
            'password.required' => 'يرجى ادخال كلمة المرور',
            'password.min' => 'كلمة المرور يجب ان تكون 8 احرف على الاقل',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين',
            'account_type.required' => 'يرجى تحديد نوع الحساب',
            'account_type.in' => 'نوع الحساب غير صحيح',
            'id_number.required_if' => 'يرجى ادخال رقم الهوية',
            'id_number.unique' => 'رقم الهوية مستخدم مسبقاً',
            'profession_id.required_if' => 'يرجى اختيار الحرفة',
            'experience_years.required_if' => 'يرجى ادخال سنوات الخبرة',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $validated = $validator->validated();

            $createdUser = DB::transaction(function () use ($validated, $isGoogleRegistration, $googleRegistration): User {
                $profile = null;

                if ($validated['account_type'] === 'craftsman') {
                    $professionId = $validated['profession_id'];

                    if ($professionId === 'other') {
                        $category = Category::firstOrCreate(
                            ['name' => 'أخرى'],
                            ['description' => 'مهن أخرى غير مصنفة']
                        );

                        $professionId = Profession::create([
                            'name' => $validated['custom_craft'],
                            'description' => 'حرفة مضافة من نموذج التسجيل',
                            'status' => 'active',
                            'category_id' => $category->id,
                        ])->id;
                    }

                    $profile = Craftman::create([
                        'id_number' => $validated['id_number'],
                        'profession_id' => $professionId,
                        'experience_years' => $validated['experience_years'],
                        'description' => $validated['description'] ?? null,
                        'hourly_rate' => $validated['hourly_rate'] ?? 0,
                        'daily_rate' => $validated['daily_rate'] ?? 0,
                    ]);
                } else {
                    $profile = Customer::create([
                        'id_number' => 'CUS-'.Str::uuid(),
                    ]);
                }

                return User::create([
                    'name' => $validated['name'],
                    'user_name' => $validated['user_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($isGoogleRegistration ? Str::random(48) : $validated['password']),
                    'google_id' => $isGoogleRegistration ? $googleRegistration['id'] : null,
                    'email_verified_at' => $isGoogleRegistration ? now() : null,
                    'mobile' => $validated['mobile'],
                    'address' => $validated['address'],
                    'gender' => $validated['gender'],
                    'date' => $validated['date'] ?? null,
                    'status' => 'active',
                    'actor_type' => $profile::class,
                    'actor_id' => $profile->id,
                ]);
            });

            if ($isGoogleRegistration) {
                $request->session()->forget('google_registration');
                $redirect = $this->loginUser($createdUser);
                $request->session()->regenerate();

                return response()->json([
                    'icon' => 'success',
                    'title' => 'تم إنشاء الحساب عبر Google بنجاح',
                    'redirect' => $redirect,
                ]);
            }

            return response()->json([
                'icon' => 'success',
                'title' => 'تم إنشاء الحساب بنجاح',
                'redirect' => route('login'),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Public registration failed.', [
                'exception' => $e,
                'account_type' => $request->input('account_type'),
            ]);

            return response()->json([
                'icon' => 'error',
                'title' => 'تعذر إنشاء الحساب. يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean',
        ], [
            'email.required' => 'يرجى ادخال البريد الالكتروني',
            'email.email' => 'يرجى ادخال بريد الكتروني صحيح',
            'password.required' => 'يرجى ادخال كلمة المرور',
        ]);

        $user = User::where('email', $request->email)->first();

        // Always run the hash check to prevent timing-based user enumeration
        $passwordValid = Hash::check(
            $request->password,
            $user?->password ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
        );

        if ($user && $passwordValid) {
            if ($user->status != 'active') {
                return response()->json([
                    'icon' => 'error',
                    'title' => 'حسابك غير مفعل',
                ], 403);
            }
            /*
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
            */
            $remember = $request->boolean('remember');

            if ($user->isCraftman()) {

                Auth::guard('craftman')->login(
                    $user,
                    $remember
                );

                $redirect = route('herafy.craftman.dashboard');
            } elseif ($user->isCustomer()) {

                Auth::guard('customer')->login(
                    $user,
                    $remember
                );

                $redirect = url('herafy/main');
            } elseif ($user->isAdmin()) {

                Auth::guard('admin')->login(
                    $user,
                    $remember
                );

                $redirect = url('cms/admin');
            } else {
                return response()->json([
                    'icon' => 'error',
                    'title' => 'This account cannot be authenticated.',
                ], 403);
            }
            $request->session()->regenerate();

            return response()->json([
                'icon' => 'success',
                'title' => 'تم تسجيل الدخول بنجاح',
                'redirect' => $redirect,
            ], 200);
        }

        return response()->json([
            'icon' => 'error',
            'title' => 'البريد الالكتروني او كلمة المرور غير صحيحة',
        ], 400);
    }

    public function logout(Request $request)
    {
        foreach (['web', 'admin', 'customer', 'craftman'] as $guard) {
            Auth::guard($guard)->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function loginUser(User $user): ?string
    {
        if ($user->isCraftman()) {
            Auth::guard('craftman')->login($user);

            return route('herafy.craftman.dashboard');
        }

        if ($user->isCustomer()) {
            Auth::guard('customer')->login($user);

            return url('herafy/main');
        }

        if ($user->isAdmin()) {
            Auth::guard('admin')->login($user);

            return url('cms/admin');
        }

        return null;
    }

    private function hasDeletedActorProfile(User $user): bool
    {
        if (! $user->actor_id) {
            return false;
        }

        return match ($user->actor_type) {
            Customer::class => Customer::onlyTrashed()->whereKey($user->actor_id)->exists(),
            Craftman::class => Craftman::onlyTrashed()->whereKey($user->actor_id)->exists(),
            default => false,
        };
    }

    private function hasPermanentlyDeletedActorProfile(User $user): bool
    {
        if (! $user->actor_id) {
            return false;
        }

        return match ($user->actor_type) {
            Customer::class => ! Customer::withTrashed()->whereKey($user->actor_id)->exists(),
            Craftman::class => ! Craftman::withTrashed()->whereKey($user->actor_id)->exists(),
            default => false,
        };
    }
}
