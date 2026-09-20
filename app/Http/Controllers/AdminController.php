<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageOptimizerService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::cmsAdmin()->latest()->paginate(10);

        return response()->view('cms.admin.index', compact('admins'));
    }

    public function create(Request $request)
    {
        $roles = Role::query()->where('guard_name', 'admin')
            ->when(! $this->isSuperAdmin($request), fn (Builder $query) => $query->where('name', '!=', 'super-admin'))
            ->get();

        return response()->view('cms.admin.create', compact('roles'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validateRequest($request, $this->rules());

        if ($validated['role_name'] === 'super-admin' && ! $this->isSuperAdmin($request)) {
            abort(403);
        }

        $role = Role::query()->where('name', $validated['role_name'])
            ->where('guard_name', 'admin')->firstOrFail();

        $admin = new User;
        $this->fillEditableFields($admin, $validated);
        $admin->password = Hash::make($validated['password']);
        $admin->actor_id = null;
        $admin->actor_type = null;
        $this->storeImage($request, $admin);

        DB::transaction(function () use ($admin, $role): void {
            $admin->save();
            $admin->assignRole($role);
        });

        return $this->successResponse($request, 'تم إنشاء المسؤول بنجاح.');
    }

    public function show($id)
    {
        $admins = $this->adminQuery()->findOrFail($id);

        return response()->view('cms.admin.show', compact('admins'));
    }

    public function edit(Request $request, $id)
    {
        $admins = $this->adminQuery()->findOrFail($id);
        $this->authorizeProtectedTarget($request, $admins);

        return response()->view('cms.admin.edit', compact('admins'));
    }

    public function update(Request $request, $id): JsonResponse|RedirectResponse
    {
        $admin = $this->adminQuery()->findOrFail($id);
        $this->authorizeProtectedTarget($request, $admin);
        $validated = $this->validateRequest($request, $this->rules($admin));

        if ($request->user('admin')?->is($admin) && $validated['status'] !== 'active') {
            abort(403, 'لا يمكنك تعطيل حسابك الحالي.');
        }

        $this->fillEditableFields($admin, $validated);
        if (! empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }
        $admin->actor_id = null;
        $admin->actor_type = null;

        $previousImage = $admin->image;
        $this->storeImage($request, $admin);
        $admin->save();

        if ($previousImage && $admin->image !== $previousImage) {
            Storage::disk('public')->delete('images/admin/'.$previousImage);
        }

        return $this->successResponse($request, 'تم تحديث المسؤول بنجاح.');
    }

    public function destroy(Request $request, $id): JsonResponse|RedirectResponse
    {
        $admin = $this->adminQuery()->findOrFail($id);
        $this->authorizeProtectedTarget($request, $admin);
        $this->denySelfTarget($request, $admin);
        $admin->delete();

        return $this->successResponse($request, 'تم حذف المسؤول بنجاح.');
    }

    public function trashed(Request $request)
    {
        $admins = $this->protectSuperAdmins($this->adminQuery(true), $request)
            ->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return response()->view('cms.admin.trashed', compact('admins'));
    }

    public function restore(Request $request, $id): RedirectResponse
    {
        $admin = $this->adminQuery(true)->findOrFail($id);
        $this->authorizeProtectedTarget($request, $admin);
        $admin->restore();

        return back()->with('success', 'تمت استعادة المدير بنجاح.');
    }

    public function force(Request $request, $id): RedirectResponse
    {
        $admin = $this->adminQuery(true)->findOrFail($id);
        $this->authorizeProtectedTarget($request, $admin);
        $this->denySelfTarget($request, $admin);
        $admin->forceDelete();

        return back()->with('success', 'تم حذف المدير نهائيًا.');
    }

    public function forceAll(Request $request): RedirectResponse
    {
        $ids = $this->validatedTrashIds($request);
        $this->protectSuperAdmins($this->adminQuery(true), $request)->whereKey($ids)->forceDelete();

        return back()->with('success', 'تم حذف جميع المدراء نهائيًا.');
    }

    private function validatedTrashIds(Request $request): array
    {
        return $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ])['ids'];
    }

    private function rules(?User $admin = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'max:45', Rule::unique('users', 'user_name')->ignore($admin?->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin?->id)],
            'password' => [$admin ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female'])],
            'mobile' => ['nullable', 'string', 'max:45'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive', 'pending', 'blocked'])],
            'address' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            ...($admin ? [] : [
                'role_name' => ['required', Rule::exists('roles', 'name')->where('guard_name', 'admin')],
            ]),
        ];
    }

    private function adminQuery(bool $onlyTrashed = false): Builder
    {
        $query = User::query();
        if ($onlyTrashed) {
            $query->onlyTrashed();
        }

        return $query->cmsAdmin();
    }

    private function protectSuperAdmins(Builder $query, Request $request): Builder
    {
        if ($this->isSuperAdmin($request)) {
            return $query;
        }

        return $query->whereDoesntHave('roles', fn (Builder $roles) => $roles
            ->where('guard_name', 'admin')->where('name', 'super-admin'));
    }

    private function authorizeProtectedTarget(Request $request, User $admin): void
    {
        if ($admin->hasRole('super-admin', 'admin') && ! $this->isSuperAdmin($request)) {
            abort(403);
        }
    }

    private function denySelfTarget(Request $request, User $admin): void
    {
        if ($request->user('admin')?->is($admin)) {
            abort(403, 'لا يمكنك حذف حسابك الحالي.');
        }
    }

    private function isSuperAdmin(Request $request): bool
    {
        return (bool) $request->user('admin')?->hasRole('super-admin', 'admin');
    }

    private function validateRequest(Request $request, array $rules): array
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                throw new ValidationException($validator);
            }

            $safeInput = $request->except(['password', 'password_confirmation']);
            throw new ValidationException($validator, redirect()->back()->withErrors($validator)->withInput($safeInput));
        }

        return $validator->validated();
    }

    private function fillEditableFields(User $admin, array $validated): void
    {
        foreach (['name', 'user_name', 'email', 'mobile', 'status', 'gender', 'address', 'date'] as $field) {
            $admin->{$field} = $validated[$field] ?? null;
        }
    }

    private function storeImage(Request $request, User $admin): void
    {
        if (! $request->hasFile('image')) {
            return;
        }

        $image = app(ImageOptimizerService::class)->forProfile($request->file('image'));
        $imageName = time().'_image.'.$image->getClientOriginalExtension();
        $image->move(public_path('storage/images/admin'), $imageName);
        $admin->image = $imageName;
    }

    private function successResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'icon' => 'success',
                'title' => $message,
                'redirect' => route('admins.index'),
            ]);
        }

        return redirect()->route('admins.index')->with('success', $message);
    }
}
