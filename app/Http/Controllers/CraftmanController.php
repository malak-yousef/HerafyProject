<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use Illuminate\Http\Request;

class CraftmanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $craftmen = Craftman::orderBy('id' , 'desc')->paginate(4);
        return response()->view('cms.craftmen.index' , compact('craftmen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Craftman $craftman)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Craftman $craftman)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Craftman $craftman)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Craftman $craftman)
    {
        //
    }
}