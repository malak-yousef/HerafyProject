<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::whereNull('actor_type')->whereNull('actor_id')->latest()->paginate(10);
        return response()->view('cms.admin.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();
        return response()->view('cms.admin.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'name'      => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users,user_name',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'password'  => 'nullable|string|min:8',
            'gender'    => 'nullable|string|in:male,female',
            'mobile'    => 'nullable|string|max:45',
            'status'    => 'required|string|in:active,inactive,pending,blocked',
            'actor_type' => 'nullable|string|in:craftman,customer,admin',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'role_name' => 'required|exists:roles,name',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $admins = new User();

            if (request()->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_image.' . $image->getClientOriginalExtension();
                $image->move('storage/images/admin', $imageName);
                //$image->storeAs('storage/images/admin' , $imageName);
                $admins->image = $imageName;
            }

            $admins->name      = $request->input('name');
            $admins->user_name = $request->input('user_name');
            $admins->email     = $request->input('email');
            $admins->password  = Hash::make($request->password);
            $admins->mobile    = $request->input('mobile');
            $admins->status    = $request->input('status');
            $admins->gender    = $request->input('gender');
            $admins->address   = $request->input('address');
            $admins->date      = $request->input('date');
            $admins->setAttribute('actor_id', null);
            $admins->setAttribute('actor_type', null);
            // $admins->assignRole('$request->role_name');
            $admins->assignRole($request->role_name);

            $isSaved = $admins->save();

            if ($isSaved) {
                return response()->json([
                    'icon' => 'success',
                    'title' => 'تم اضافة مشرف بنجاح',
                ], 200);
            } else {
                return response()->json(['icon' => 'error', 'title' => 'فشلت عملية الحفظ'], 400);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $admins = User::findOrFail($id);
        return response()->view('cms.admin.show', compact('admins'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $admins = User::findOrFail($id);
        return response()->view('cms.admin.edit', compact('admins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = validator($request->all(), [
            'name'  => 'required|string|max:255',
            'user_name'  => 'required|string|max:255|unique:users,user_name,' . $id,
            'email'  => 'required|string|email|max:255|unique:users,email,' . $id,
            'password'  => 'nullable|string|min:8',
            'gender'  => 'nullable|string|in:male,female',
            'mobile'  => 'nullable|string|max:45',
            'status'  => 'required|string|in:active,inactive,pending,blocked',
            'actor_type'  => 'nullable|string|in:craftman,customer,admin',
            'image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $admins = User::findOrFail($id);

            if (request()->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_image.' . $image->getClientOriginalExtension();
                $image->move('storage/images/admin', $imageName);
                //$image->storeAs('storage/images/admin' , $imageName);
                $admins->image = $imageName;
            }

            $admins->name = $request->input('name');
            $admins->user_name = $request->input('user_name');
            $admins->email = $request->input('email');
            if ($request->filled('password')) {
                $admins->password = Hash::make($request->password);
            }

            $admins->mobile = $request->input('mobile');
            $admins->status = $request->input('status');
            $admins->gender = $request->input('gender');
            $admins->address = $request->input('address');
            $admins->date = $request->input('date');
            $admins->setAttribute('actor_id', null);
            $admins->setAttribute('actor_type', null);
            $isSaved = $admins->save();

            if ($isSaved) {
                return response()->json([
                    'icon' => 'success',
                    'title' => 'تم تعديل مشرف بنجاح',
                    'redirect' => route('admins.index'),
                ], 200);
            }

            return response()->json([
                'icon' => 'error',
                'title' => 'فشلت عملية الحفظ',
            ], 400);
        }
    }
    /**
     * Remove the specified resource from storage.
     */


    public function destroy($id)
    {
        $isDeleted = User::destroy($id);

        return response()->json([
            'icon' => $isDeleted ? 'success' : 'error',
            'title' => $isDeleted ? 'تم الحذف بنجاح' : 'فشل الحذف',
        ], $isDeleted ? 200 : 400);
    }


    public function trashed()
    {
        $admins = User::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return response()->view('cms.admin.trashed', compact('admins'));
    }

    public function restore($id)
    {
        $admins = User::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Success');
    }

    public function force($id)
    {
        $admins = User::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Success');
    }

    public function forceAll()
    {
        $admins = User::onlyTrashed()->forceDelete();
        return back()->with('success', 'Success');
    }
}
