<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use Illuminate\Http\Request;

class CraftmanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $craftmen = Craftman::orderBy('id', 'desc')->paginate(4);
        return response()->view('cms.craftmen.index', compact('craftmen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.craftmen.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // الطريقة الاولى لاستقبال البيانات 
        $validator = validator(
            $request->all(),
            [
                'name' => 'required |string|min:3|max:50',
                'craft_name' => 'required |string|min:3|max:50',
                'email' => 'required |email',
                'password' => 'required |string|min:3|max:50',
                'experience_years' => 'required |numeric',
                'description' => 'required |string|min:3|max:255',
                'hourly_rate' => 'required |numeric',
                'daily_rate' => 'required |numeric',
                'is_active' => 'required |boolean',
            ],
            messages: [
                'required' => 'هذا الحقل مطلوب',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'icon' => 'خطأ في البيانات',
                'titel' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $craftmen = new Craftman();
            $craftmen->name = $request->input('name');
            $craftmen->craft_name = $request->input('craft_name');
            $craftmen->email = $request->input('email');
            $craftmen->password = $request->input('password');
            $craftmen->experience_years = $request->input('experience_years');
            $craftmen->description = $request->input('description');
            $craftmen->hourly_rate = $request->input('hourly_rate');
            $craftmen->daily_rate = $request->input('daily_rate');
            $craftmen->is_active = $request->input('is_active');
            $isSaved = $craftmen->save();
            return response()->json([
                'icon' => 'success',
                'title' => 'تم الحفظ بنجاح',
            ], 200);
        }

        // الطريقة الثانية لاستقبال البيانات
        // $validated = $request->validate([
        //     'name' => 'required |string|min:3|max:50',
        //     'craft_name' => 'required |string|min:3|max:50',
        //     'email' => 'required |email',
        //     // 'password' => 'required |string|min:3|max:50',
        //     'experience_years' => 'required |numeric',
        //     'description' => 'required |string|min:3|max:255',
        //     'hourly_rate' => 'required |numeric',
        //     'daily_rate' => 'required |numeric',
        //     'is_active' => 'required |boolean',
        // ], [
        //     'required' => 'هذا الحقل مطلوب', // رسالة خاصة للحقل المطلوب
        // ]);
        // $validated['password'] = bcrypt('123456789'); // كلمة مرور افتراضية
        // $craftmen = Craftman::create($validated);
        // return response()->json([
        //     'icon' => $craftmen ? 'تم بنجاح' : ' خطاء',
        //     'title' => $craftmen ? 'تم الحفظ بنجاح' : 'خطاء في الحفظ',
        // ], $craftmen ? 200 : 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $craftmen = Craftman::findOrFail($id);
        return response()->view('cms.craftmen.show', compact('craftmen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $craftmen = Craftman::findOrFail($id);
        return response()->view('cms.craftmen.edit', compact('craftmen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = validator(
            $request->all(),
            [
                'name' => 'nullable|string|min:3|max:50',
                'craft_name' => 'nullable|string|min:3|max:50',
                'email' => 'nullable|email',
                'password' => 'nullable|string|min:3|max:50',
                'experience_years' => 'nullable|numeric',
                'description' => 'nullable|string|min:3|max:255',
                'hourly_rate' => 'nullable|numeric',
                'daily_rate' => 'nullable|numeric',
                'is_active' => 'nullable|boolean',
            ],
            [
                'required' => 'هذا الحقل مطلوب',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'icon' => 'خطأ في البيانات',
                'title' => $validator->getMessageBag()->first(), // صححت كلمة titel → title
            ], 400);
        }

        $craftmen = Craftman::findOrFail($id);
        $craftmen->name = $request->input('name');
        $craftmen->craft_name = $request->input('craft_name');
        // البريد الإلكتروني: إذا الحقل disabled، الأفضل ما تحدثه
        if ($request->filled('email')) {
            $craftmen->email = $request->input('email');
        }
        // كلمة المرور: تشفير
        if ($request->filled('password')) {
            $craftmen->password = bcrypt($request->input('password'));
        }
        // $craftmen->phone = $request->input('phone');
        $craftmen->experience_years = $request->input('experience_years');
        $craftmen->description = $request->input('description');
        $craftmen->hourly_rate = $request->input('hourly_rate');
        $craftmen->daily_rate = $request->input('daily_rate');
        $craftmen->is_active = $request->input('is_active');

        $craftmen->save();

        return ['redirect' => route('craftmen.index')];
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $craftmen = Craftman::destroy($id);
    }
}