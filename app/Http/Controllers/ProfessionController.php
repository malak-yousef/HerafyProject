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

        $professions = Profession::with('category')->withCount('craftsmen')
            ->latest()
            ->withoutTrashed()
            ->paginate(15)->withQueryString();

        return response()->view('cms.profession.index', compact('professions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return response()->view('cms.profession.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|min:4|max:20',
            'description' => 'required|max:100',
            'category_id' => 'required',
            'status' => 'required|in:active,in_active',
        ], [
            'name.required' => 'هذا الحقل مطلوب',
            'description.required' => 'هذا الحقل مطلوب',
            'name.min' => 'لا يقبل اقل من 4 حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
            'category_id.required' => 'يرجى اختيار التصنيف.',
            'category_id.exists' => 'التصنيف المختار غير موجود.',
        ]);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['icon' => 'error', 'title' => $validator->getMessageBag()->first()], 400);
            }

            return back()->withErrors($validator)->withInput();
        } else {

            $professions = new Profession;
            $professions->name = $request->input('name');
            $professions->description = $request->input('description');
            $professions->status = $request->input('status');
            $professions->category_id = $request->input('category_id');

            $isUpdated = $professions->save();

            return $request->expectsJson() ? response()->json(['icon' => 'success', 'title' => 'Created is Successfully']) : redirect()->route('professions.index')->with('success', 'تمت إضافة المهنة بنجاح.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $categories = Category::all();
        $professions = Profession::findOrFail($id);

        return response()->view('cms.profession.show', compact('professions', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = Category::all();
        $professions = Profession::findOrFail($id);

        return response()->view('cms.profession.edit', compact('professions', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator($request->all(), [
            'name' => 'sometimes|required|string|min:4|max:20',
            'description' => 'sometimes|required|max:100',
            'category_id' => 'sometimes|required',
            'status' => 'sometimes|required|in:active,in_active',
        ], [
            'name.required' => 'هذا الحقل مطلوب',
            'description.required' => 'هذا الحقل مطلوب',
            'name.min' => 'لا يقبل اقل من 4 حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
            'category_id.required' => 'يرجى اختيار التصنيف.',

        ]);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['icon' => 'error', 'title' => $validator->getMessageBag()->first()], 400);
            }

            return back()->withErrors($validator)->withInput();
        } else {

            $professions = Profession::findOrFail($id);
            $professions->name = $request->input('name');
            $professions->description = $request->input('description');
            $professions->status = $request->input('status');
            $professions->category_id = $request->input('category_id');

            $isSavaved = $professions->save();

            return $request->expectsJson() ? ['redirect' => route('professions.index')] : redirect()->route('professions.index')->with('success', 'تم تحديث المهنة بنجاح.');
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
        $professions = Profession::onlyTrashed()->with('category')->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return response()->view('cms.profession.trashed', compact('professions'));
    }

    public function restore($id)
    {
        $professions = Profession::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Success');
    }

    public function force($id)
    {
        $professions = Profession::onlyTrashed()->findOrFail($id)->forceDelete();

        return back()->with('success', 'Success');
    }

    public function forceAll(Request $request)
    {
        Profession::onlyTrashed()->whereKey($this->trashIds($request))->forceDelete();

        return back()->with('success', 'Success');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }
}
