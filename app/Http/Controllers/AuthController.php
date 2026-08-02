<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function showLogin()
    {
        return response()->view('cms.auth.login');
    }

    public function showRegister()
    {
        $professions = Profession::where('status', 'active')->get();
        return response()->view('cms.auth.register', compact('professions'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'user_name'        => 'required|string|max:45|unique:users,user_name',
            'email'            => 'required|email|max:255|unique:users,email',
            'password'         => 'required|string|min:6|confirmed',
            'mobile'           => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'gender'           => 'required|in:male,female',
            'date'             => 'nullable|date',
            'account_type'     => 'required|in:user,craftsman',
            // حقول الحرفي
            'id_number'        => 'required_if:account_type,craftsman|numeric|unique:craftmen,id_number',
            'profession_id'    => 'required_if:account_type,craftsman',
            'experience_years' => 'required_if:account_type,craftsman|integer|min:0',
            'hourly_rate'      => 'nullable|numeric|min:0',
            'daily_rate'       => 'nullable|numeric|min:0',
            'description'      => 'nullable|string',
            // حرفة مخصصة (أخرى)
            'custom_craft'     => 'nullable|string|max:255',
        ], [
            'name.required'                => 'يرجى ادخال الاسم الكامل',
            'user_name.required'           => 'يرجى ادخال اسم المستخدم',
            'user_name.unique'             => 'اسم المستخدم مستخدم مسبقاً',
            'email.required'               => 'يرجى ادخال البريد الالكتروني',
            'email.email'                  => 'يرجى ادخال بريد الكتروني صحيح',
            'email.unique'                 => 'البريد الالكتروني مستخدم مسبقاً',
            'password.required'            => 'يرجى ادخال كلمة المرور',
            'password.min'                 => 'كلمة المرور يجب ان تكون 6 احرف على الاقل',
            'password.confirmed'           => 'كلمتا المرور غير متطابقتين',
            'account_type.required'        => 'يرجى تحديد نوع الحساب',
            'account_type.in'              => 'نوع الحساب غير صحيح',
            'id_number.required_if'        => 'يرجى ادخال رقم الهوية',
            'id_number.unique'             => 'رقم الهوية مستخدم مسبقاً',
            'profession_id.required_if'    => 'يرجى اختيار الحرفة',
            'experience_years.required_if' => 'يرجى ادخال سنوات الخبرة',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'icon'  => 'error',
                'title' => $validator->getMessageBag()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // إعداد بيانات المهنة
            $professionId = $request->profession_id;
            if ($request->account_type == 'craftsman' && $request->profession_id === 'other') {
                // التأكد من وجود قسم "أخرى" أو إنشاؤه
                $category = Category::firstOrCreate(
                    ['name' => 'أخرى'],
                    ['description' => 'مهن أخرى غير مصنفة']
                );

                $profession = Profession::create([
                    'name'        => $request->custom_craft ?? 'حرفة أخرى',
                    'description' => 'حرفة مضافة من نموذج التسجيل',
                    'status'      => 'active',
                    'category_id' => $category->id,
                ]);
                $professionId = $profession->id;
            }

            if ($request->account_type == 'craftsman') {
                $craftman = Craftman::create([
                    'id_number'        => $request->id_number,
                    'profession_id'    => $professionId,
                    'experience_years' => $request->experience_years ?? 0,
                    'description'      => $request->description,
                    'hourly_rate'      => $request->hourly_rate ?? 0,
                    'daily_rate'       => $request->daily_rate ?? 0,
                ]);

                $user = User::create([
                    'name'       => $request->name,
                    'user_name'  => $request->user_name,
                    'email'      => $request->email,
                    'password'   => Hash::make($request->password),
                    'mobile'     => $request->mobile,
                    'address'    => $request->address,
                    'gender'     => $request->gender,
                    'date'       => $request->date,
                    'status'     => 'active',
                    'actor_type' => Craftman::class,
                    'actor_id'   => $craftman->id,
                ]);
            } else {
                $customer = Customer::create([
                    'id_number' => $request->id_number ?? 'CUS-' . time(),
                ]);

                $user = User::create([
                    'name'       => $request->name,
                    'user_name'  => $request->user_name,
                    'email'      => $request->email,
                    'password'   => Hash::make($request->password),
                    'mobile'     => $request->mobile,
                    'address'    => $request->address,
                    'gender'     => $request->gender,
                    'date'       => $request->date,
                    'status'     => 'active',
                    'actor_type' => Customer::class,
                    'actor_id'   => $customer->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'icon'  => 'success',
                'title' => 'تم إنشاء الحساب بنجاح',
                'redirect' => route('login'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'icon'  => 'error',
                'title' => 'حدث خطأ أثناء إنشاء الحساب: ' . $e->getMessage(),
            ], 500);
        }
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
