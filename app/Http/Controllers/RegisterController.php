<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Craftman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegister()
    {
        return response()->view('cms.auth.register');
    }

    public function register(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'account_type' => 'required|in:user,craftsman',
            // حقول الحرفي (تظهر فقط عند اختيار حرفي)
            'craft_name' => 'required_if:account_type,craftsman|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // إنشاء المستخدم
            $user = User::create([
                'name' => $request->name,
                'user_name' => $request->user_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'status' => 'active',
            ]);

            // إذا كان المستخدم حرفي، إنشاء بيانات الحرفي
            if ($request->account_type == 'craftsman') {
                Craftman::create([
                    'user_id' => $user->id,
                    'craft_name' => $request->craft_name,
                    'experience_years' => $request->experience_years ?? 0,
                    'description' => $request->description,
                    'hourly_rate' => $request->hourly_rate ?? 0,
                    'daily_rate' => $request->daily_rate ?? 0,
                    'is_available' => true,
                ]);
            }

            DB::commit();

            return redirect()->route('login')
                ->with('success', 'تم إنشاء الحساب بنجاح. يرجى تسجيل الدخول.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الحساب: ' . $e->getMessage())
                ->withInput();
        }
    }
}
