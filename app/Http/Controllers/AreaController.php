<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;

use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::with('city')
            ->orderBy('id', 'desc')
            ->withoutTrashed()
            ->paginate(4);

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
            'name' => 'required|string|min:4|max:20',

            'city_id' => 'required|exists:cities,id',

        ], [
            'name.required' => "هذا الحقل مطلوب",

            'name.min' => 'لا يقبل اقل من 4 حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
            'city_id.required' => 'يرجى اختيار التصنيف.',
            'city_id.exists' => 'التصنيف المختار غير موجود.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {

            $areas = new Area();
            $areas->name = $request->input('name');
            $areas->city_id = $request->input('city_id');


            $isUpdated = $areas->save();

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
            'name' => 'required|string|min:4|max:20',
            'city_id' => 'required|exists:cities,id',
        ], [
            'name.required' => "هذا الحقل مطلوب",

            'name.min' => 'لا يقبل اقل من 4 حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
            'city_id.required' => 'يرجى اختيار التصنيف.',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {

            $areas = Area::findOrFail($id);
            $areas->name = $request->input('name');


            $areas->city_id = $request->input('city_id');


            $isSavaved = $areas->save();

            return ['redirect' => route('areas.index')];
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
        $areas = Area::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return response()->view('cms.area.trashed', compact('areas'));
    }

    public function restore($id)
    {
        $areas = Area::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Success');
    }

    public function force($id)
    {
        $areas = Area::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Success');
    }

    public function forceAll()
    {
        $areas = Area::onlyTrashed()->forceDelete();
        return back()->with('success', 'Success');
    }
}
