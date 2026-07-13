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
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.customer.create');
    }
    // public function store(Request $request)
    // {
    //     $validator = validator($request->all(), []);
    //     if (! $validator->fails()) {
    //         $customers = new Customer();
    //         $customers->email    = $request->input('email');
    //         $customers->password = Hash::make($request->password);

    //         $isSaved = $customers->save();
    //         if ($isSaved) {
    //             $users = new User();
    //             if (request()->hasFile('image')) {
    //                 $image     = $request->file('image');
    //                 $imageName = time() . 'image.' . $image->getClientOriginalExtension();
    //                 $image->move('storage/images/customer', $imageName);
    //                 $users->image = $imageName;
    //             }

    //             $users->first_name = $request->input('first_name');
    //             $users->last_name  = $request->input('last_name');
    //             $users->email      = $request->input('email');
    //             $users->mobile     = $request->input('mobile');
    //             $users->address    = $request->input('address');
    //             $users->date       = $request->input('date');
    //             $users->password   = Hash::make($request->password);
    //             $users->status     = $request->input('status');
    //             $users->gender     = $request->input('gender');

    //             $users->actor()->associate($customers);
    //             $isSaved = $users->save();

    //             return response()->json([
    //                 'icon'  => 'success',
    //                 'title' => 'Created Customer is Successfully',
    //             ], 200);
    //         }
    //     } else {
    //         return response()->json([
    //             'icon'  => 'error',
    //             'title' => $validator->getMessageBag()->first(),
    //         ], 400);
    //     }
    // }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'first_name' => 'required |string|min:3|max:50',
                'last_name'  => 'required |string|min:3|max:50',
                'password'   => 'required |string|min:3|max:50:customers,password',
                'email'      => 'required |email |unique:customers,email',
                'mobile'     => 'nullable |string|min:3|max:20',
                'address'    => 'nullable |string|min:3|max:255',
                'date'       => 'required |date',
                'gender'     => 'required |in:male,female',
                'status'     => 'required |in:pending,active,inactive,blocked',
                'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            messages: [
                'required'   => 'هذا الحقل مطلوب',
                'string'     => 'هذا الحقل يجب ان يكون نص',
                'email'      => 'هذا الحقل يجب ان يكون بريد الكتروني صالح',
                'unique'     => 'هذا الحقل يجب ان يكون فريد',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'icon' => 'خطأ في البيانات',
                'titel' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $customers = new Customer();
            $customers->email    = $request->input('email');
            $customers->password = Hash::make($request->password);

            $isSaved = $customers->save();
            if ($isSaved) {
                $users = new User();

                if (request()->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = time() . '_' . $image->getClientOriginalExtension();
                    $image->move('storage/images/customer', $imageName);
                    $users->image  = $imageName;
                }
                $users->first_name = $request->input('first_name');
                $users->last_name  = $request->input('last_name');
                $users->mobile     = $request->input('mobile');
                $users->address    = $request->input('address');
                $users->date       = $request->input('date');
                $users->status     = $request->input('status');
                $users->gender     = $request->input('gender');
                $users->actor()->associate($customers);

                $isSaved = $users->save();
            }

            if ($isSaved) {
                return response()->json([
                    'icon' => 'success',
                    'titel' => 'تم الحفظ بنجاح',
                ], 200);
            } else {
                return response()->json([
                    'icon' => 'error',
                    'titel' => 'فشل الحفظ',
                ], 400);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
