<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->view('cms.spatie.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.spatie.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'name'       => 'required|unique:permissions,name',
                'guard_name' => 'required|string|in:admin,user,craftman',
            ],
            [
                'name.required' => 'اسم الدور مطلوب',
                'name.unique' => 'اسم الدور موجود مسبقا',
                'guard_name.required' => 'الصلاحيات مطلوبة',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $permissions = new Permission();
            $permissions->name = $request->input('name');
            $permissions->guard_name = $request->input('guard_name');
            $isSaved = $permissions->save();

            if (!$isSaved) {
                return response()->json([
                    'icon' => 'error',
                    'title' => 'حدث خطأ اثناء الحفظ',
                ], 400);
            } else {
                return response()->json([
                    'icon' => 'success',
                    'title' => 'تم الحفظ بنجاح',
                ], 200);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $permissions = Permission::destroy($id);
    }
}
