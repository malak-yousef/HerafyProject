<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::where('guard_name', 'admin')->get();

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
                'name' => [
                    'required',
                    Rule::unique('permissions', 'name')->where('guard_name', 'admin'),
                ],
            ],
            [
                'name.required' => 'اسم الصلاحية مطلوب',
                'name.unique' => 'اسم الصلاحية موجود مسبقًا',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $permissions = new Permission;
            $permissions->name = $request->input('name');
            $permissions->guard_name = 'admin';
            $isSaved = $permissions->save();

            if (! $isSaved) {
                return response()->json([
                    'icon' => 'error',
                    'title' => 'حدث خطأ أثناء حفظ الصلاحية',
                ], 400);
            } else {
                return response()->json([
                    'icon' => 'success',
                    'title' => 'تم حفظ الصلاحية بنجاح',
                ], 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Permission::where('guard_name', 'admin')->findOrFail($id)->delete();
    }
}
