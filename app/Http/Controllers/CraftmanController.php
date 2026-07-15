<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CraftmanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $craftmen = Craftman::orderBy('id', 'desc')->withoutTrashed()->paginate(4);
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
                'name'             => 'required |string|min:3|max:50',
                'user_name'        => 'required |string|min:3|max:50|unique:users,user_name',
                'id_number'        => 'required |numeric|unique:craftmen,id_number',
                'mobile'           => 'nullable |numeric',
                'craft_name'       => 'required |string|min:3|max:50',
                'email'            => 'required |email|unique:users,email',
                'password'         => 'required |string|min:3|max:50',
                'experience_years' => 'required |numeric',
                'description'      => 'nullable |string|min:3|max:255',
                'hourly_rate'      => 'required |numeric',
                'daily_rate'       => 'required |numeric',
                'status'           => 'required |in:pending,active,inactive,blocked,',
                'address'          => 'nullable |string|min:3|max:255',
                'gender'           => 'required |in:male,female',
                'image'            => 'nullable |image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            messages: [
                'required' => 'هذا الحقل مطلوب',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $craftmen = new Craftman();
            $craftmen->id_number        = $request->input('id_number');
            $craftmen->craft_name       = $request->input('craft_name');
            $craftmen->experience_years = $request->input('experience_years');
            $craftmen->description      = $request->input('description');
            $craftmen->hourly_rate      = $request->input('hourly_rate');
            $craftmen->daily_rate       = $request->input('daily_rate');
            $isSaved = $craftmen->save();

            if ($isSaved) {
                $users = new User();

                if (request()->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->move('storage/images/craftman', $imageName);
                    $users->image = $imageName;
                }

                $users->name      = $request->input('name');
                $users->user_name = $request->input('user_name');
                $users->email     = $request->input('email');
                $users->password  = Hash::make($request->input('password'));
                $users->address   = $request->input('address');
                $users->mobile    = $request->input('mobile');
                $users->gender    = $request->input('gender');
                $users->status    = $request->input('status');
                $users->actor()->associate($craftmen);
                $isSaved = $users->save();
            }

            if (!$isSaved) {
                return response()->json([
                    'icon'  => 'error',
                    'title' => 'لم يتم حفظ بيانات الحرفي',
                ], 400);
            } else {
                return response()->json([
                    'icon' => 'success',
                    'title' => 'تم الحفظ بنجاح',
                ], 200);
            }
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
        $craftmen = Craftman::findOrFail($id);
        $user = $craftmen->user;
        if (!$user) {
            return response()->json([
                'icon' => 'error',
                'title' => 'خطأ',
                'text' => 'لا يوجد مستخدم مرتبط بهذا الحرفي',
            ], 400);
        }
        $user_id = $user->id;
        $validator = validator(
            $request->all(),
            [
                'name'             => 'required|string|min:3|max:50',
                'user_name'        => 'required|string|min:3|max:50|unique:users,user_name,' . $user_id,
                'id_number'        => 'required|numeric|unique:craftmen,id_number,' . $id,
                'mobile'           => 'nullable|numeric',
                'craft_name'       => 'required|string|min:3|max:50',
                'email'            => 'required|email|unique:users,email,' . $user_id,
                'password'         => 'nullable|string|min:3|max:50',
                'experience_years' => 'required|numeric',
                'description'      => 'nullable|string|min:3|max:255',
                'hourly_rate'      => 'required|numeric',
                'daily_rate'       => 'required|numeric',
                'status'           => 'required|string|in:pending,active,inactive,blocked',
                'address'          => 'nullable|string|min:3|max:255',
                'gender'           => 'required|in:male,female',
                'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'required' => 'هذا الحقل مطلوب',
            ]
        );
        if ($validator->fails()) {

            return response()->json([
                'icon'  => 'error',
                'title' => 'خطأ في البيانات',
                'text'  => $validator->getMessageBag()->first(),
            ], 400);
        }
        $craftmen->id_number        = $request->input('id_number');
        $craftmen->craft_name       = $request->input('craft_name');
        $craftmen->experience_years = $request->input('experience_years');
        $craftmen->description      = $request->input('description');
        $craftmen->hourly_rate      = $request->input('hourly_rate');
        $craftmen->daily_rate       = $request->input('daily_rate');

        $craftmen->save();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(
                public_path('storage/images/craftman'),
                $imageName
            );
            $user->image = $imageName;
        }
        $user->name      = $request->input('name');
        $user->user_name = $request->input('user_name');
        $user->email     = $request->input('email');
        $user->mobile    = $request->input('mobile');
        $user->address   = $request->input('address');
        $user->gender    = $request->input('gender');
        $user->status    = $request->input('status');
        if ($request->filled('password')) {
            $user->password = Hash::make(
                $request->input('password')
            );
        }
        
        $user->save();

        return [
            'redirect' => route('craftmen.index')
        ];
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $craftmen = Craftman::destroy($id);
    }
    public function trashed()
    {
        $craftmen = Craftman::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return response()->view('cms.craftmen.trashed', compact('craftmen'));
    }

    public function restore($id)
    {
        $craftmen = Craftman::onlyTrashed()->findOrFail($id);
        $craftmen->restore();
        return redirect()->route('craftmen_trashed')->with('success', 'تم استعادة الحرفي بنجاح.');
    }
    public function forceDestroy($id)
    {
        $craftmen = Craftman::onlyTrashed()->findOrFail($id);
        $craftmen->forceDelete();
        return redirect()->route('craftmen_trashed')->with('success', 'تم حذف الحرفي بشكل دائم.');
    }
    public function DestroyAll()
    {
        $craftmen = Craftman::onlyTrashed()->forceDelete();
        return redirect()->route('craftmen_trashed')->with('success', 'تم حذف جميع الحرفيين بشكل دائم.');
    }
}
