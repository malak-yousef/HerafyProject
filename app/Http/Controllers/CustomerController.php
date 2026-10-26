<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'account_status' => ['nullable', Rule::in(['active', 'inactive', 'blocked'])],
            'date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'name_asc', 'name_desc'])],
        ]);
        $query = Customer::query()->with('user');
        if ($search = trim($validated['search'] ?? '')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('user_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }
        if (isset($validated['account_status'])) {
            $query->whereHas('user', fn ($q) => $q->where('status', $validated['account_status']));
        }
        if (isset($validated['date_from'])) {
            $query->whereHas('user', fn ($q) => $q->whereDate('created_at', '>=', $validated['date_from']));
        }
        if (isset($validated['date_to'])) {
            $query->whereHas('user', fn ($q) => $q->whereDate('created_at', '<=', $validated['date_to']));
        }
        $sort = $validated['sort'] ?? 'newest';
        if ($sort === 'newest') {
            $query->latest('customers.created_at');
        } elseif ($sort === 'oldest') {
            $query->oldest('customers.created_at');
        } else {
            $query->orderBy(User::select('name')->whereColumn('users.actor_id', 'customers.id')->where('users.actor_type', Customer::class), $sort === 'name_asc' ? 'asc' : 'desc');
        }
        $customers = $query->paginate(15)->withQueryString();

        return response()->view('cms.customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.customer.create');
    }

    public function store(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'name' => 'required |string|min:3|max:50',
                'user_name' => 'required |string|min:3|max:45|unique:users,user_name',
                'id_number' => 'required |numeric|unique:customers,id_number',
                'mobile' => 'nullable|string|max:45',
                'email' => 'required |email|unique:users,email',
                'password' => 'required|string|min:8|max:50',
                'status' => 'required |in:pending,active,inactive,blocked,',
                'address' => 'nullable |string|min:3|max:255',
                'gender' => 'required |in:male,female',
                'date' => 'nullable|date',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ],
            messages: [
                'required' => 'هذا الحقل مطلوب',
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
            $customers = new Customer;
            $customers->id_number = $request->input('id_number');

            $users = new User;
            $imageName = null;

            if (request()->hasFile('image')) {
                $image = app(ImageOptimizerService::class)->forProfile($request->file('image'));
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('storage/images/customer'), $imageName);
                $users->image = $imageName;
            }

            $users->name = $request->input('name');
            $users->user_name = $request->input('user_name');
            $users->email = $request->input('email');
            $users->password = Hash::make($request->input('password'));
            $users->address = $request->input('address');
            $users->mobile = $request->input('mobile');
            $users->gender = $request->input('gender');
            $users->date = $request->input('date');
            $users->status = $request->input('status');
            $users->actor()->associate($customers);

            $isSaved = DB::transaction(fn (): bool => $customers->save() && $users->save());

            if (! $isSaved && $imageName) {
                Storage::disk('public')->delete('images/customer/'.$imageName);
            }

            if (! $isSaved) {
                if (! $request->expectsJson()) {
                    return back()->withErrors(['form' => 'لم يتم حفظ بيانات الزبون'])->withInput($request->except('password'));
                }

                return response()->json([
                    'icon' => 'error',
                    'title' => 'لم يتم حفظ بيانات الزبون',
                ], 400);
            } else {
                if (! $request->expectsJson()) {
                    return redirect()->route('customers.index')->with('success', 'تم الحفظ بنجاح');
                }

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
    /* public function show($id)
    {
        $customers = Customer::findOrFail($id);
        return response()->view('cms.customer.show', compact('customers'));



    } */
    public function show($id)
    {
        $customers = Customer::with([
            'user',
            'orders.city',
            'orders.area',
        ])->findOrFail($id);

        return response()->view('cms.customer.show', compact('customers'));
    }
    /*public function show($id)
        {
            $orders = Order::all();
            $cities = City::all();
            $areas = Area::all();
            $customers = Customer::findOrFail($id);
            return response()->view('cms.customer.show', compact('customers', 'orders' , 'cities' , 'areas'));
        }*/

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $customers = Customer::findOrFail($id);

        return response()->view('cms.customer.edit', compact('customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customers = Customer::findOrFail($id);
        $user = $customers->user;
        if (! $user) {
            return response()->json([
                'icon' => 'error',
                'title' => 'خطأ',
                'text' => 'لا يوجد مستخدم مرتبط بهذا الزبون',
            ], 400);
        }
        $user_id = $user->id;
        $validator = validator(
            $request->all(),
            [
                'name' => 'required |string|min:3|max:50',
                'user_name' => 'required |string|min:3|max:45|unique:users,user_name,'.$user_id,
                'id_number' => 'required |numeric|unique:customers,id_number,'.$id,
                'mobile' => 'nullable|string|max:45',
                'email' => 'required |email|unique:users,email,'.$user_id,
                'password' => 'nullable|string|min:8|max:50',
                'status' => 'required |in:pending,active,inactive,blocked',
                'address' => 'nullable |string|min:3|max:255',
                'gender' => 'required |in:male,female',
                'date' => 'nullable|date',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ],
            messages: [
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
        $customers->id_number = $request->input('id_number');
        $oldImage = $user->image;
        $newImageName = null;

        if ($request->hasFile('image')) {
            $image = app(ImageOptimizerService::class)->forProfile($request->file('image'));
            $newImageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(
                public_path('storage/images/customer'),
                $newImageName
            );
            $user->image = $newImageName;
        }
        $user->name = $request->input('name');
        $user->user_name = $request->input('user_name');
        $user->email = $request->input('email');
        $user->address = $request->input('address');
        $user->mobile = $request->input('mobile');
        $user->gender = $request->input('gender');
        $user->date = $request->input('date');
        $user->status = $request->input('status');
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        [$customerSaved, $userSaved] = DB::transaction(function () use ($customers, $user): array {
            return [$customers->save(), $user->save()];
        });

        if ($newImageName !== null) {
            if ($userSaved && $oldImage) {
                Storage::disk('public')->delete('images/customer/'.$oldImage);
            } elseif (! $userSaved) {
                Storage::disk('public')->delete('images/customer/'.$newImageName);
            }
        }

        if (! $request->expectsJson()) {
            return redirect()->route('customers.index')->with('success', 'تم تحديث بيانات الزبون بنجاح');
        }

        return [
            'redirect' => route('customers.index'),
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        Customer::destroy($id);

        if ($request->expectsJson()) {
            return ['redirect' => route('customers.index')];
        }

        return redirect()->route('customers.index')->with('success', 'تم حذف العميل بنجاح.');
    }

    public function trashed()
    {
        $customers = Customer::onlyTrashed()->with('user')->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return response()->view('cms.customer.trashed', compact('customers'));
    }

    public function restore($id)
    {
        $customers = Customer::onlyTrashed()->findOrFail($id);
        $customers->restore();

        return redirect()->route('customers_trashed')->with('success', 'تم استعادة الزبون بنجاح.');
    }

    public function force($id)
    {
        $customers = Customer::onlyTrashed()->findOrFail($id);
        $user = $customers->user()->withTrashed()->first();

        DB::transaction(function () use ($customers, $user): void {
            $customers->forceDelete();
            $user?->forceDelete();
        });

        return redirect()->route('customers_trashed')->with('success', 'تم حذف العميل بشكل دائم.');
    }

    public function forceAll(Request $request)
    {
        $customers = Customer::onlyTrashed()->whereKey($this->trashIds($request))->get();

        DB::transaction(function () use ($customers): void {
            $customers->each(function (Customer $customer): void {
                $user = $customer->user()->withTrashed()->first();
                $customer->forceDelete();
                $user?->forceDelete();
            });
        });

        return redirect()->route('customers_trashed')->with('success', 'تم حذف العملاء المحددين بشكل دائم.');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }

    public function orders(Customer $customer)
    {
        $orders = Order::with(['city', 'area'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        return view('cms.customer.orders', compact('customer', 'orders'));
    }
}
