<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::orderBy('id', 'desc')->withoutTrashed()->paginate(4);
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
                'name'             => 'required |string|min:3|max:50',
                'user_name'        => 'required |string|min:3|max:50|unique:users,user_name',
                'id_number'        => 'required |numeric|unique:customers,id_number',
                'mobile'           => 'nullable |numeric',
                'email'            => 'required |email|unique:users,email',
                'password'         => 'required |string|min:3|max:50',
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
            $customers = new Customer();
            $customers->id_number = $request->input('id_number');
            $isSaved = $customers->save();

            if ($isSaved) {
                $users = new User();

                if (request()->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->move('storage/images/customer', $imageName);
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
                $users->actor()->associate($customers);
                $isSaved = $users->save();
            }

            if (!$isSaved) {
                return response()->json([
                    'icon'  => 'error',
                    'title' => 'لم يتم حفظ بيانات الزبون',
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
    public function show($id)
    {
        $customers = Customer::findOrFail($id);
        return response()->view('cms.customer.show', compact('customers'));
    }

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
                'name'             => 'required |string|min:3|max:50',
                'user_name'        => 'required |string|min:3|max:50|unique:users,user_name,' . $user_id,
                'id_number'        => 'required |numeric|unique:customers,id_number,' . $id,
                'mobile'           => 'nullable |numeric',
                'email'            => 'required |email|unique:users,email,' . $user_id,
                'password'         => 'nullable |string|min:3|max:50',
                'status'           => 'required |in:pending,active,inactive,blocked',
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
                'icon'  => 'error',
                'title' => 'خطأ في البيانات',
                'text'  => $validator->getMessageBag()->first(),
            ], 400);
        }
        $customers->id_number = $request->input('id_number');
        $isSaved = $customers->save();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(
                public_path('storage/images/customer'),
                $imageName
            );
            $user->image = $imageName;
        }
        $user->name      = $request->input('name');
        $user->user_name = $request->input('user_name');
        $user->email     = $request->input('email');
        $user->address   = $request->input('address');
        $user->mobile    = $request->input('mobile');
        $user->gender    = $request->input('gender');
        $user->status    = $request->input('status');
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $isSaved = $user->save();
        return [
            'redirect' => route('customers.index')
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customers = Customer::destroy($id);
    }
    public function trashed()
    {
        $customers = Customer::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return response()->view('cms.customer.trashed', compact('customers'));
    }

    public function restore($id)
    {
        $customers = Customer::onlyTrashed()->findOrFail($id);
        $customers->restore();
        return redirect()->route('customers_trashed')->with('success', 'تم استعادة الحرفي بنجاح.');
    }
    public function forceDestroy($id)
    {
        $customers = Customer::onlyTrashed()->findOrFail($id);
        $customers->forceDelete();
        return redirect()->route('customers_trashed')->with('success', 'تم حذف الحرفي بشكل دائم.');
    }
    public function DestroyAll()
    {
        $customers = Customer::onlyTrashed()->forceDelete();
        return redirect()->route('customers_trashed')->with('success', 'تم حذف جميع الحرفيين بشكل دائم.');
    }
}
