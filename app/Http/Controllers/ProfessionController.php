<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        
         $professions = Profession::orderBy('id' , 'desc')->withoutTrashed()->paginate(4);
        return response()->view('cms.profession.index' , compact('professions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
         return response()->view('cms.profession.create' , compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(),[
            'name' => 'required|string|min:4|max:20' ,
            'description' => 'required|max:100',
            'category_id' =>'required',
            'status' => 'required|in:active,in_active',
        ] , [
            'name.required' => "هذا الحقل مطلوب",
            'description.required' => "هذا الحقل مطلوب",
            'name.min' => 'لا يقبل اقل من 4 حروف',
             'name.max' => 'لا يقبل اكثر من 20حرف',
             'category_id.required' => 'يرجى اختيار التصنيف.',
'category_id.exists' => 'التصنيف المختار غير موجود.',
        ]);
        if( $validator->fails()){
            return response()->json([
                'icon' => 'error' ,
                'title' => $validator->getMessageBag()->first(),
            ] , 400);
        }
        else{
             
        $professions = new Profession();
        $professions->name = $request->input('name');
         $professions->description = $request->input('description');
          $professions->status = $request->input('status');
         $professions->category_id = $request->input('category_id');
         
        
         $isUpdated = $professions->save();

         return response()->json([
             'icon' => 'success' ,
                'title' => 'Created is Successfully',
         ] , 200);
        }
        
    }

    
    

    /**
     * Display the specified resource.
     */
    
        public function show($id)
    {
          $categories = Category::all();
         $professions = Profession::findOrFail($id);
        return response()->view('cms.profession.show' , compact('professions' , 'categories'));
    }

    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = Category::all();
        $professions = Profession::findOrFail($id);
        return response()->view('cms.profession.edit' , compact('professions' , 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator($request->all(),[
            'name' => 'sometimes|required|string|min:4|max:20' ,
            'description' => 'sometimes|required|max:100',
            'category_id' =>'sometimes|required',
            'status' => 'sometimes|required|in:active,in_active',
        ] , [
            'name.required' => "هذا الحقل مطلوب",
            'description.required' => "هذا الحقل مطلوب",
            'name.min' => 'لا يقبل اقل من 4 حروف',
             'name.max' => 'لا يقبل اكثر من 20حرف',
             'category_id.required' => 'يرجى اختيار التصنيف.',

        ]);
        if( $validator->fails()){
            return response()->json([
                'icon' => 'error' ,
                'title' => $validator->getMessageBag()->first(),
            ] , 400);
        }
        else{
             
        $professions = Profession::findOrFail($id);
        $professions->name = $request->input('name');
         $professions->description = $request->input('description');
          $professions->status = $request->input('status');
         $professions->category_id = $request->input('category_id');
         
        
         $isSavaved = $professions->save();

       return ['redirect' =>route('professions.index')];
    }

    }    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $professions = Profession::destroy($id);
    }
public function trashed()
    {
        $professions = Profession::onlyTrashed()->orderBy('deleted_at' , 'desc')->get();
        return response()->view('cms.profession.trashed' , compact('professions'));
    }

    public function restore($id){
      $professions = Profession::onlyTrashed()->findOrFail($id)->restore();
     return back()->with('success' , 'Success');
    }

    public function force($id){
      $professions = Profession::onlyTrashed()->findOrFail($id)->forceDelete();
     return back()->with('success' , 'Success');
    }

    public function forceAll(){
      $professions = Profession::onlyTrashed()->forceDelete();
     return back()->with('success' , 'Success');
    }
}
