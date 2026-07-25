<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('professions')->orderBy('id', 'desc')->withoutTrashed()->paginate(4);
        return response()->view('cms.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return response()->view('cms.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string|min:4|max:20',
            'description' => 'required|max:100'
        ], [
            'name.required' => "هذا الحقل مطلوب",
            'description.required' => "هذا الحقل مطلوب",
            'name.min' => 'لا يقبل اقل من 4 حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $categories = new Category();
            $categories->name = $request->input('name');
            $categories->description = $request->input('description');

            $isSavaved = $categories->save();

            return response()->json([
                'icon' => 'success',
                'title' => 'Created is Successfully',
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $categories = Category::findOrFail($id);
        return response()->view('cms.category.show', compact('categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = Category::findOrFail($id);
        return response()->view('cms.category.edit', compact('categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|min:4|max:20',
            'description' => 'required|max:100'
        ], [
            'name.required' => "هذا الحقل مطلوب",
            'description.required' => "هذا الحقل مطلوب",
            'name.min' => 'لا يقبل اقل من4حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
        ]);

        $categories = new Category();
        $categories->name = $request->input('name');
        $categories->description = $request->input('description');

        if (! $validator->fails()) {

            $categories = Category::findOrFail($id);
            $categories->name = $request->input('name');
            $categories->description = $request->input('description');
            $isUpdated = $categories->save();
            return ['redirect' => route('categories.index')];

            //  return response()->json([
            //   'icon' => 'success' ,
            //       'title' => 'Updated is Successfully',
            //   ] , 200);
        } else {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $categories = Category::destroy($id);
    }

    public function trashed()
    {
        $categories = Category::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return response()->view('cms.category.trashed', compact('categories'));
    }

    public function restore($id)
    {
        $categories = Category::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Success');
    }

    public function force($id)
    {
        $categories = Category::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Success');
    }

    public function forceAll()
    {
        $categories = Category::onlyTrashed()->forceDelete();
        return back()->with('success', 'Success');
    }
}
