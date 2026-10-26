<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Profession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])],
            'profession_id' => ['nullable', Rule::exists('professions', 'id')->whereNull('deleted_at')],
            'city_id' => ['nullable', Rule::exists('cities', 'id')->whereNull('deleted_at')],
            'area_id' => ['nullable', Rule::exists('areas', 'id')->whereNull('deleted_at')],
            'date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'service_date_from' => ['nullable', 'date'], 'service_date_to' => ['nullable', 'date', 'after_or_equal:service_date_from'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'service_date_asc', 'service_date_desc', 'budget_asc', 'budget_desc'])],
        ]);
        if (! empty($validated['city_id']) && ! empty($validated['area_id']) && ! Area::whereKey($validated['area_id'])->where('city_id', $validated['city_id'])->exists()) {
            throw ValidationException::withMessages(['area_id' => 'المنطقة لا تتبع المدينة المحددة.']);
        }
        $query = Order::query()->with([
            'customer.user',
            'profession',
            'city',
            'area',
        ]);
        if ($search = trim($validated['search'] ?? '')) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhereHas('customer.user', fn ($u) => $u->where('name', 'like', "%{$search}%")));
        }
        foreach (['status', 'profession_id', 'city_id', 'area_id'] as $field) {
            if (isset($validated[$field])) {
                $query->where($field, $validated[$field]);
            }
        }
        if (isset($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }
        if (isset($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }
        if (isset($validated['service_date_from'])) {
            $query->whereDate('service_date', '>=', $validated['service_date_from']);
        }
        if (isset($validated['service_date_to'])) {
            $query->whereDate('service_date', '<=', $validated['service_date_to']);
        }
        $sort = $validated['sort'] ?? 'newest';
        if ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif (str_starts_with($sort, 'service_date_')) {
            $query->orderBy('service_date', str_ends_with($sort, 'desc') ? 'desc' : 'asc');
        } else {
            $query->orderBy('budget', str_ends_with($sort, 'desc') ? 'desc' : 'asc');
        }
        $orders = $query->paginate(15)->withQueryString();
        $professions = Profession::query()->whereNull('deleted_at')->orderBy('name')->get();
        $cities = City::query()->whereNull('deleted_at')->orderBy('name')->get();
        $areas = Area::query()->whereNull('deleted_at')->orderBy('name')->get();

        return view('cms.order.index', compact('orders', 'professions', 'cities', 'areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //  $users = Customer::with('admin')->get();
        $customers = Customer::with('user')->get();
        $cities = City::all();
        $areas = Area::all();
        $professions = Profession::all();

        return view('cms.order.create', compact(
            'customers',
            'cities',
            'areas',
            'professions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([

            'customer_id' => 'required|exists:customers,id',
            'profession_id' => 'required|exists:professions,id',
            'city_id' => ['required', Rule::exists('cities', 'id')->whereNull('deleted_at')],
            'area_id' => [
                'required',
                Rule::exists('areas', 'id')->where(fn ($query) => $query
                    ->where('city_id', $request->input('city_id'))
                    ->whereNull('deleted_at')),
            ],

            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',

            'service_date' => 'nullable|date',

            'budget' => 'nullable|numeric',

            'status' => 'nullable|in:pending,accepted,in_progress,completed,cancelled',
        ]);

        Order::create($data);

        return redirect()
            ->route('orders.index')
            ->with('success', 'تم إضافة الطلب بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load([
            'customer.user',
            'profession',
            'city',
            'area',
            'images',
            'offers.craftman.user',
            'serviceJob.craftman.user',
        ]);

        return view('cms.order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {

        $customers = Customer::with('user')->get();
        $cities = City::all();
        $areas = Area::all();
        $professions = Profession::all();

        return view('cms.order.edit', compact(
            'order',
            'customers',
            'cities',
            'areas',
            'professions'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {

        $data = $request->validate([

            'customer_id' => 'required|exists:customers,id',
            'profession_id' => 'required|exists:professions,id',
            'city_id' => ['required', Rule::exists('cities', 'id')->whereNull('deleted_at')],
            'area_id' => [
                'required',
                Rule::exists('areas', 'id')->where(fn ($query) => $query
                    ->where('city_id', $request->input('city_id'))
                    ->whereNull('deleted_at')),
            ],

            'title' => 'required|string|max:255',
            'description' => 'required|string',

            'phone' => 'nullable|string|max:20',

            'address' => 'required|string',

            'service_date' => 'nullable|date',

            'budget' => 'nullable|numeric',

            'status' => 'required|in:pending,accepted,in_progress,completed,cancelled',

        ]);

        $order->update($data);

        return redirect()
            ->route('orders.index')
            ->with('success', 'تم تعديل الطلب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $orders = Order::destroy($id);
    }

    public function trashed()
    {
        $orders = Order::onlyTrashed()
            ->with(['customer.user', 'profession', 'city', 'area'])
            ->orderByDesc('deleted_at')
            ->paginate(15)->withQueryString();

        return response()->view('cms.order.trashed', compact('orders'));
    }

    public function restore($id)
    {
        $orders = Order::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'تمت استعادة الطلب بنجاح.');
    }

    public function force($id)
    {
        $orders = Order::onlyTrashed()->findOrFail($id)->forceDelete();

        return back()->with('success', 'تم حذف الطلب نهائيًا.');
    }

    public function forceAll(Request $request)
    {
        Order::onlyTrashed()->whereKey($this->trashIds($request))->forceDelete();

        return back()->with('success', 'تم حذف جميع الطلبات نهائيًا.');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }
}
