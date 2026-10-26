<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::with('city')->withCount('orders')
            ->latest()
            ->withoutTrashed()
            ->paginate(15)->withQueryString();

        return response()->view('cms.area.index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cities = City::all();

        return response()->view('cms.area.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => [
                'required',
                'string',
                'min:4',
                'max:20',
                Rule::unique('areas', 'name')->where('city_id', $request->input('city_id')),
            ],
            'city_id' => 'required|exists:cities,id',

        ], [
            'name.required' => 'هذا الحقل مطلوب',
            'name.unique' => 'اسم المنطقة مستخدم مسبقاً في هذه المدينة',
            'name.min' => 'لا يقبل اقل من 4 حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
            'city_id.required' => 'يرجى اختيار المدينة.',
            'city_id.exists' => 'المدينة المختارة غير موجودة.',
        ]);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['icon' => 'error', 'title' => $validator->getMessageBag()->first()], 400);
            }

            return back()->withErrors($validator)->withInput();
        } else {

            $areas = new Area;
            $areas->name = $request->input('name');
            $areas->city_id = $request->input('city_id');

            $isUpdated = $areas->save();

            return $request->expectsJson() ? response()->json(['icon' => 'success', 'title' => 'Created is Successfully']) : redirect()->route('areas.index')->with('success', 'تمت إضافة المنطقة بنجاح.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $cities = City::all();
        $areas = Area::findOrFail($id);

        return response()->view('cms.area.show', compact('areas', 'cities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cities = City::all();
        $areas = Area::findOrFail($id);

        return response()->view('cms.area.edit', compact('areas', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator($request->all(), [
            'name' => [
                'required',
                'string',
                'min:4',
                'max:20',
                Rule::unique('areas', 'name')->where('city_id', $request->input('city_id'))->ignore($id),
            ],
            'city_id' => 'required|exists:cities,id',
        ], [
            'name.required' => 'هذا الحقل مطلوب',
            'name.unique' => 'اسم المنطقة مستخدم مسبقاً في هذه المدينة',
            'name.min' => 'لا يقبل اقل من 4 حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
            'city_id.required' => 'يرجى اختيار المدينة.',

        ]);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['icon' => 'error', 'title' => $validator->getMessageBag()->first()], 400);
            }

            return back()->withErrors($validator)->withInput();
        } else {

            $areas = Area::findOrFail($id);
            $areas->name = $request->input('name');

            $areas->city_id = $request->input('city_id');

            $isSavaved = $areas->save();

            return $request->expectsJson() ? ['redirect' => route('areas.index')] : redirect()->route('areas.index')->with('success', 'تم تحديث المنطقة بنجاح.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $areas = Area::destroy($id);
    }

    public function trashed()
    {
        $areas = Area::onlyTrashed()->with('city')->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return response()->view('cms.area.trashed', compact('areas'));
    }

    public function restore($id)
    {
        $areas = Area::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'تمت استعادة المنطقة بنجاح.');
    }

    public function force($id)
    {
        $areas = Area::onlyTrashed()->findOrFail($id)->forceDelete();

        return back()->with('success', 'تم حذف المنطقة نهائيًا.');
    }

    public function forceAll(Request $request)
    {
        Area::onlyTrashed()->whereKey($this->trashIds($request))->forceDelete();

        return back()->with('success', 'تم حذف جميع المناطق نهائيًا.');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }
}
