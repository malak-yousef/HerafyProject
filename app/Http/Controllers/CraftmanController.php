<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Profession;
use App\Models\User;
use App\Models\Verification;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CraftmanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'profession_id' => ['nullable', Rule::exists('professions', 'id')->whereNull('deleted_at')],
            'account_status' => ['nullable', Rule::in(['active', 'inactive', 'blocked'])],
            'verification_status' => ['nullable', Rule::in(['pending', 'approved', 'rejected', 'unverified'])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'name_asc', 'name_desc', 'experience_desc', 'experience_asc'])],
        ]);
        $query = Craftman::query()->with(['profession', 'user', 'verifications' => fn ($q) => $q->latest('id')->limit(1)]);
        if ($search = trim($validated['search'] ?? '')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('user_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }
        if (isset($validated['profession_id'])) {
            $query->where('profession_id', $validated['profession_id']);
        }
        if (isset($validated['account_status'])) {
            $query->whereHas('user', fn ($q) => $q->where('status', $validated['account_status']));
        }
        if (isset($validated['verification_status'])) {
            $latestIds = Verification::query()->selectRaw('MAX(id)')->groupBy('craftman_id');
            if ($validated['verification_status'] === 'unverified') {
                $query->whereDoesntHave('verifications');
            } else {
                $query->whereHas('verifications', fn ($q) => $q->whereIn('id', $latestIds)->where('status', $validated['verification_status']));
            }
        }
        $sort = $validated['sort'] ?? 'newest';
        if ($sort === 'newest') {
            $query->latest('craftmen.created_at');
        } elseif ($sort === 'oldest') {
            $query->oldest('craftmen.created_at');
        } elseif ($sort === 'name_asc' || $sort === 'name_desc') {
            $query->orderBy(User::select('name')->whereColumn('users.actor_id', 'craftmen.id')->where('users.actor_type', Craftman::class), $sort === 'name_asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('experience_years', str_ends_with($sort, 'desc') ? 'desc' : 'asc');
        }
        $craftmen = $query->paginate(15)->withQueryString();
        $professions = Profession::query()->whereNull('deleted_at')->orderBy('name')->get();

        return response()->view('cms.craftmen.index', compact('craftmen', 'professions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $professions = Profession::all();

        return response()->view('cms.craftmen.create', compact('professions'));
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
                'user_name' => 'required |string|min:3|max:45|unique:users,user_name',
                'id_number' => 'required |numeric|unique:craftmen,id_number',
                'mobile' => 'nullable|string|max:45',
                // 'craft_name'       => 'required |string|min:3|max:50',
                'profession_id' => 'required',
                'email' => 'required |email|unique:users,email',
                'password' => 'required|string|min:8|max:50',
                'experience_years' => 'required |numeric',
                'description' => 'nullable |string|min:3|max:255',
                'hourly_rate' => 'required |numeric',
                'daily_rate' => 'required |numeric',
                'status' => 'required |in:pending,active,inactive,blocked',
                'address' => 'nullable |string|min:3|max:255',
                'gender' => 'required |in:male,female',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'is_featured' => 'nullable|boolean',
            ],
            messages: [
                'required' => 'هذا الحقل مطلوب',
                'profession_id.required' => 'يرجى اختيار الحرفة.',
                'profession_id.exists' => 'الحرفة المختارة غير موجود.',
            ]
        );

        if ($validator->fails()) {
            if (! $request->expectsJson()) {
                return back()->withErrors($validator)->withInput($request->except('password'));
            }

            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            if ($request->boolean('is_featured') && Craftman::where('is_featured', true)->count() >= 4) {
                return back()->withErrors(['is_featured' => 'يمكن اختيار 4 حرفيين كحد أقصى ضمن أبرز الحرفيين.'])->withInput($request->except('password'));
            }

            $craftmen = new Craftman;
            $craftmen->id_number = $request->input('id_number');
            // $craftmen->craft_name       = $request->input('craft_name');
            $craftmen->profession_id = $request->input('profession_id');
            $craftmen->experience_years = $request->input('experience_years');
            $craftmen->description = $request->input('description');
            $craftmen->hourly_rate = $request->input('hourly_rate');
            $craftmen->daily_rate = $request->input('daily_rate');
            $craftmen->is_featured = $request->boolean('is_featured');

            $users = new User;
            $imageName = null;

            if (request()->hasFile('image')) {
                $image = app(ImageOptimizerService::class)->forProfile($request->file('image'));
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('storage/images/craftman'), $imageName);
                $users->image = $imageName;
            }

            $users->name = $request->input('name');
            $users->user_name = $request->input('user_name');
            $users->email = $request->input('email');
            $users->password = Hash::make($request->input('password'));
            $users->address = $request->input('address');
            $users->mobile = $request->input('mobile');
            $users->gender = $request->input('gender');
            $users->status = $request->input('status');
            $users->actor()->associate($craftmen);

            $isSaved = DB::transaction(fn (): bool => $craftmen->save() && $users->save());

            if (! $isSaved && $imageName) {
                Storage::disk('public')->delete('images/craftman/'.$imageName);
            }

            if (! $isSaved) {
                if (! $request->expectsJson()) {
                    return back()->withErrors(['form' => 'لم يتم حفظ بيانات الحرفي'])->withInput($request->except('password'));
                }

                return response()->json([
                    'icon' => 'error',
                    'title' => 'لم يتم حفظ بيانات الحرفي',
                ], 400);
            } else {
                if (! $request->expectsJson()) {
                    return redirect()->route('craftmen.index')->with('success', 'تم الحفظ بنجاح');
                }

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
        $professions = Profession::all();
        $craftmen = Craftman::findOrFail($id);

        return response()->view('cms.craftmen.show', compact('craftmen', 'professions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $professions = Profession::all();
        $craftmen = Craftman::findOrFail($id);

        return response()->view('cms.craftmen.edit', compact('craftmen', 'professions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $craftmen = Craftman::findOrFail($id);
        $user = $craftmen->user;
        if (! $user) {
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
                'name' => 'required|string|min:3|max:50',
                'user_name' => 'required|string|min:3|max:45|unique:users,user_name,'.$user_id,
                'id_number' => 'required|numeric|unique:craftmen,id_number,'.$id,
                'mobile' => 'nullable|string|max:45',
                //  'craft_name'       => 'required|string|min:3|max:50',
                'profession_id' => 'sometimes|required',
                'email' => 'required|email|unique:users,email,'.$user_id,
                'password' => 'nullable|string|min:8|max:50',
                'experience_years' => 'required|numeric',
                'description' => 'nullable|string|min:3|max:255',
                'hourly_rate' => 'required|numeric',
                'daily_rate' => 'required|numeric',
                'status' => 'required|string|in:pending,active,inactive,blocked',
                'address' => 'nullable|string|min:3|max:255',
                'gender' => 'required|in:male,female',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'is_featured' => 'nullable|boolean',
            ],
            [
                'required' => 'هذا الحقل مطلوب',
            ]
        );
        if ($validator->fails()) {
            if (! $request->expectsJson()) {
                return back()->withErrors($validator)->withInput($request->except('password'));
            }

            return response()->json([
                'icon' => 'error',
                'title' => 'خطأ في البيانات',
                'text' => $validator->getMessageBag()->first(),
            ], 400);
        }
        if ($request->boolean('is_featured') && ! $craftmen->is_featured
            && Craftman::where('is_featured', true)->where('id', '!=', $craftmen->id)->count() >= 4) {
            return back()->withErrors(['is_featured' => 'يمكن اختيار 4 حرفيين كحد أقصى ضمن أبرز الحرفيين.'])->withInput($request->except('password'));
        }
        $craftmen->id_number = $request->input('id_number');
        //  $craftmen->craft_name       = $request->input('craft_name');
        $craftmen->profession_id = $request->input('profession_id');
        $craftmen->experience_years = $request->input('experience_years');
        $craftmen->description = $request->input('description');
        $craftmen->hourly_rate = $request->input('hourly_rate');
        $craftmen->daily_rate = $request->input('daily_rate');
        $craftmen->is_featured = $request->boolean('is_featured');

        $oldImage = $user->image;
        $newImageName = null;

        if ($request->hasFile('image')) {
            $image = app(ImageOptimizerService::class)->forProfile($request->file('image'));
            $newImageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('storage/images/craftman'), $newImageName);
            $user->image = $newImageName;
        }

        $user->name = $request->input('name');
        $user->user_name = $request->input('user_name');
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        $user->address = $request->input('address');
        $user->gender = $request->input('gender');
        $user->status = $request->input('status');
        if ($request->filled('password')) {
            $user->password = Hash::make(
                $request->input('password')
            );
        }

        [$craftmanSaved, $userSaved] = DB::transaction(function () use ($craftmen, $user): array {
            return [$craftmen->save(), $user->save()];
        });

        if ($newImageName !== null) {
            if ($userSaved && $oldImage) {
                Storage::disk('public')->delete('images/craftman/'.$oldImage);
            } elseif (! $userSaved) {
                Storage::disk('public')->delete('images/craftman/'.$newImageName);
            }
        }

        if (! $request->expectsJson()) {
            return redirect()->route('craftmen.index')->with('success', 'تم تحديث بيانات الحرفي بنجاح');
        }

        return [
            'redirect' => route('craftmen.index'),
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        Craftman::destroy($id);

        if ($request->expectsJson()) {
            return ['redirect' => route('craftmen.index')];
        }

        return redirect()->route('craftmen.index')->with('success', 'تم حذف الحرفي بنجاح.');
    }

    public function trashed()
    {
        $craftmen = Craftman::onlyTrashed()->with(['user', 'profession'])->orderByDesc('deleted_at')->paginate(15)->withQueryString();

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

    public function DestroyAll(Request $request)
    {
        Craftman::onlyTrashed()->whereKey($this->trashIds($request))->forceDelete();

        return redirect()->route('craftmen_trashed')->with('success', 'تم حذف جميع الحرفيين بشكل دائم.');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }
}
