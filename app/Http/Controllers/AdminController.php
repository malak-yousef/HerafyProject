<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $admins = Admin::orderBy('id', 'desc')->paginate(10);
        return response()->view('cms.admin.index' , compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.admin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(),[
           
        ]);
        if(! $validator->fails()){
          $admins = new Admin();
          $admins->email = $request->input('email');
          // $admins->password = $request->input('password');  
            $admins->password = Hash::make($request->password);

           $isSaved = $admins->save();
           if($isSaved){
            $users = new User();
            

                if (request()->hasFile('image')) {
                     $image = $request->file('image');
                      $imageName = time() . 'image.' . $image->getClientOriginalExtension();
                  $image->move('storage/images/admin' , $imageName);
                  //$image->storeAs('storage/images/admin' , $imageName);
                  $users->image = $imageName;
                   }

               $users->first_name = $request->input('first_name');
               $users->last_name = $request->input('last_name');
               $users->email = $request->input('email');
               $users->mobile = $request->input('mobile');
               $users->address = $request->input('address');
               $users->date = $request->input('date');
             
              // $users->password = $request->input('password');
              $users->password = Hash::make($request->password);
               $users->status = $request->input('status');
                  $users->gender = $request->input('gender');
                  
                  $users->actor()->associate($admins);
                  $isSaved = $users->save();

                  return response()->json([
             'icon' => 'success' ,
                'title' => 'Created Admin is Successfully',
         ] , 200);
           }
        }
        else{
         return response()->json([
             'icon' => 'error' ,
                'title' => $validator->getMessageBag()->first(),
         ] , 400);
        }
        
    }

    
    

    /**
     * Display the specified resource.
     */


    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $admins = Admin::findOrFail($id);
        return response()->view('cms.admin.show' , compact('admins'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $admins = Admin::findOrFail($id);
        return response()->view('cms.admin.edit' , compact('admins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator($request->all() , [
          // 'password' =>'required',
        ]);
        if(! $validator->fails()){
          $admins = Admin::findOrFail($id);
          $admins->email = $request->input('email');
          // $admins->password = $request->input('password'); 

           $isSaved = $admins->save();
           if($isSaved){
            $users = $admins->user;
            

            
                if (request()->hasFile('image')) {
                     $image = $request->file('image');
                      $imageName = time() . 'image.' . $image->getClientOriginalExtension();
                  $image->move('storage/images/admin' , $imageName);
                  //$image->storeAs('storage/images/admin' , $imageName);
                  $users->image = $imageName;
                   }
                  
               $users->first_name = $request->input('first_name');
               $users->last_name = $request->input('last_name');
            
               $users->mobile = $request->input('mobile');
               $users->address = $request->input('address');
               $users->date = $request->input('date');
             //  $users->image = $request->get('image');
               
               $users->status = $request->input('status');
                  $users->gender = $request->input('gender');
                  
                  $users->actor()->associate($admins);
                  $isSaved = $users->save();

                  return ['redirect' =>route('admins.index')];
           }
        }
        else{
         return response()->json([
             'icon' => 'error' ,
                'title' => $validator->getMessageBag()->first(),
         ] , 400);
        }
        
    }

    

    /**
     * Remove the specified resource from storage.
     */
    
    
    public function destroy($id)
    {
        
        $admins = Admin::destroy($id);
    }

}


