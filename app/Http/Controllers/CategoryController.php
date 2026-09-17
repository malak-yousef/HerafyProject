<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('professions')->latest()->withoutTrashed()->paginate(15)->withQueryString();

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
            'name' => 'required|string|min:4|max:20|unique:categories,name',
            'description' => 'required|max:100',
        ], [
            'name.required' => 'هذا الحقل مطلوب',
            'name.unique' => 'اسم التصنيف مستخدم مسبقاً',
            'description.required' => 'هذا الحقل مطلوب',
            'name.min' => 'لا يقبل اقل من 4 حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
        ]);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['icon' => 'error', 'title' => $validator->getMessageBag()->first()], 400);
            }

            return back()->withErrors($validator)->withInput();
        } else {
            $categories = new Category;
            $categories->name = $request->input('name');
            $categories->description = $request->input('description');

            $isSavaved = $categories->save();

            return $request->expectsJson() ? response()->json(['icon' => 'success', 'title' => 'Created is Successfully']) : redirect()->route('categories.index')->with('success', 'تمت إضافة التصنيف بنجاح.');
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
            'name' => ['required', 'string', 'min:4', 'max:20', Rule::unique('categories', 'name')->ignore($id)],
            'description' => 'required|max:100',
        ], [
            'name.required' => 'هذا الحقل مطلوب',
            'name.unique' => 'اسم التصنيف مستخدم مسبقاً',
            'description.required' => 'هذا الحقل مطلوب',
            'name.min' => 'لا يقبل اقل من4حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
        ]);

        $categories = new Category;
        $categories->name = $request->input('name');
        $categories->description = $request->input('description');

        if (! $validator->fails()) {

            $categories = Category::findOrFail($id);
            $categories->name = $request->input('name');
            $categories->description = $request->input('description');
            $isUpdated = $categories->save();

            return $request->expectsJson() ? ['redirect' => route('categories.index')] : redirect()->route('categories.index')->with('success', 'تم تحديث التصنيف بنجاح.');

            //  return response()->json([
            //   'icon' => 'success' ,
            //       'title' => 'Updated is Successfully',
            //   ] , 200);
        } else {
            if ($request->expectsJson()) {
                return response()->json(['icon' => 'error', 'title' => $validator->getMessageBag()->first()], 400);
            }

            return back()->withErrors($validator)->withInput();
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
        $categories = Category::onlyTrashed()->orderByDesc('deleted_at')->paginate(15)->withQueryString();

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

    public function forceAll(Request $request)
    {
        Category::onlyTrashed()->whereKey($this->trashIds($request))->forceDelete();

        return back()->with('success', 'Success');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }
}
