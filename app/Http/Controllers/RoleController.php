<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->withCount('permissions')->get();

        return response()->view('cms.spatie.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.spatie.roles.create');
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
                    Rule::unique('roles', 'name')->where('guard_name', 'admin'),
                ],
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
            $roles = new Role;
            $roles->name = $request->input('name');
            $roles->guard_name = 'admin';
            $isSaved = $roles->save();

            if (! $isSaved) {
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Role::where('guard_name', 'admin')->findOrFail($id)->delete();
    }

    public function editRolePermissions(string $id)
    {
        $role = Role::where('guard_name', 'admin')->findOrFail($id);

        $permissions = Permission::where('guard_name', $role->guard_name)->get();

        // جلب أسماء الصلاحيات التي يمتلكها الدور حالياً لتعليمها بـ Checked
        $rolePermissions = $role->permissions()->pluck('id')->toArray();

        return view('cms.spatie.roles.role-permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    // 2. حفظ وتحديث الصلاحيات للدور في قاعدة البيانات
    public function updateRolePermissions(Request $request, string $id)
    {
        $role = Role::where('guard_name', 'admin')->findOrFail($id);

        // إذا لم يتم تحديد أي Checkbox، نمرر مصفوفة فارغة لإلغاء كل الصلاحيات
        $validated = $request->validate([
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where('guard_name', 'admin'),
            ],
        ]);

        $permissions = Permission::where('guard_name', 'admin')
            ->whereIn('id', $validated['permissions'] ?? [])
            ->get();

        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'تم تحديث صلاحيات الدور بنجاح');
    }
}
