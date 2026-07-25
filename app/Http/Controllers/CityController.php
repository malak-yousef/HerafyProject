<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::withCount('areas', 'orders')->orderBy('id', 'desc')->withoutTrashed()->paginate(4);
        return response()->view('cms.city.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->view('cms.city.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator($request->all(), [
            'name' => 'required|string|min:4|max:20',

        ], [
            'name.required' => "هذا الحقل مطلوب",

            'name.min' => 'لا يقبل اقل من 4 حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $cities = new City();
            $cities->name = $request->input('name');


            $isSavaved = $cities->save();

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
        $cities = City::with(['areas', 'orders.customer.user'])->findOrFail($id);
        return response()->view('cms.city.show', compact('cities'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cities = City::findOrFail($id);
        return response()->view('cms.city.edit', compact('cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|min:4|max:20',

        ], [
            'name.required' => "هذا الحقل مطلوب",

            'name.min' => 'لا يقبل اقل من4حروف',
            'name.max' => 'لا يقبل اكثر من 20حرف',
        ]);

        $cities = new City();
        $cities->name = $request->input('name');


        if (! $validator->fails()) {

            $cities = City::findOrFail($id);
            $cities->name = $request->input('name');

            $isUpdated = $cities->save();
            return ['redirect' => route('cities.index')];

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
        $cities = City::destroy($id);
    }

    public function trashed()
    {
        $cities = City::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return response()->view('cms.city.trashed', compact('cities'));
    }

    public function restore($id)
    {
        $cities = City::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Success');
    }

    public function force($id)
    {
        $cities = City::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Success');
    }

    public function forceAll()
    {
        $cities = City::onlyTrashed()->forceDelete();
        return back()->with('success', 'Success');
    }
}
