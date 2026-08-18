<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of all settings.
     */
    public function index()
    {
        $settings = Setting::latest()->get();

        return view('cms.setting.index', compact('settings'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create()
    {
        return view('cms.setting.create');
    }

    /**
     * Store a newly created setting.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
        ]);

        Setting::create($validated);

        return redirect()
            ->route('settings.index')
            ->with('success', 'تمت إضافة الإعداد بنجاح');
    }

    /**
     * Display the specified setting.
     */
    public function show(Setting $setting)
    {
        return view('cms.setting.show', compact('setting'));
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit(Setting $setting)
    {
        return view('cms.setting.edit', compact('setting'));
    }

    /**
     * Update the specified setting.
     */
    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'value' => 'nullable|string',
        ]);

        $setting->update($validated);

        return redirect()
            ->route('settings.index')
            ->with('success', 'تم تعديل الإعداد بنجاح');
    }

    /**
     * Soft delete the specified setting.
     */
    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()
            ->route('settings.index')
            ->with('success', 'تم حذف الإعداد بنجاح');
    }

    /**
     * Display trashed settings.
     */
    public function trashed()
    {
        $settings = Setting::onlyTrashed()
            ->latest('deleted_at')
            ->get();

        return view('cms.setting.trashed', compact('settings'));
    }

    /**
     * Restore the specified setting.
     */
    public function restore($id)
    {
        $setting = Setting::onlyTrashed()->findOrFail($id);

        $setting->restore();

        return redirect()
            ->route('settings.trashed')
            ->with('success', 'تم استرجاع الإعداد بنجاح');
    }

    /**
     * Permanently delete the specified setting.
     */
    public function forceDelete($id)
    {
        $setting = Setting::onlyTrashed()->findOrFail($id);

        $setting->forceDelete();

        return redirect()
            ->route('settings.trashed')
            ->with('success', 'تم حذف الإعداد نهائيًا');
    }
}