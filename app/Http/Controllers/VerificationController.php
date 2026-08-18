<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    // عرض جميع طلبات التوثيق
    public function index()
    {
        $verifications = Verification::with('craftman.user')
            ->latest()
            ->paginate(10);

        return view('cms.verification.index', compact('verifications'));
    }


    // عرض تفاصيل طلب توثيق للأدمن
    public function show(Verification $verification)
    {
        $verification->load([
            'craftman',
            'craftman.profession'
        ]);

        return view('cms.verification.show', compact('verification'));
    }


    // صفحة مراجعة طلب التوثيق
    public function edit(Verification $verification)
    {
        $verification->load([
            'craftman',
            'craftman.profession'
        ]);

        return view('cms.verification.edit', compact('verification'));
    }


    // تحديث حالة التوثيق
    public function update(Request $request, Verification $verification)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $verification->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->route('verifications.index')
            ->with('success', 'تم تحديث حالة التوثيق بنجاح');
    }


    // حذف طلب التوثيق
    public function destroy(Verification $verification)
    {
        $verification->delete();

        return redirect()
            ->route('verifications.index')
            ->with('success', 'تم حذف طلب التوثيق بنجاح');
    }


    // التوثيقات المحذوفة
    public function trashed()
    {
        $verifications = Verification::onlyTrashed()
            ->with('craftman')
            ->latest()
            ->paginate(10);

        return view(
            'cms.verification.trashed',
            compact('verifications')
        );
    }


    // استعادة التوثيق
    public function restore($id)
    {
        $verification = Verification::withTrashed()
            ->findOrFail($id);

        $verification->restore();

        return redirect()
            ->route('verifications.trashed')
            ->with('success', 'تم استعادة طلب التوثيق بنجاح');
    }


    /*
    |--------------------------------------------------------------------------
    | Craftman
    |--------------------------------------------------------------------------
    */

    // صفحة التوثيق للحرفي
    public function create()
    {
        $craftman = auth()->user()->actor;

        $verification = Verification::where('craftman_id', $craftman->id)
            ->latest()
            ->first();

        return view(
            'herafy.verification.create',
            compact('verification')
        );
    }


    // حفظ طلب التوثيق
    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $craftman = auth()->user()->actor;

        /*
        |--------------------------------------------------------------------------
        | التأكد من عدم وجود طلب قيد المراجعة أو مقبول
        |--------------------------------------------------------------------------
        */

        $existingVerification = Verification::where(
            'craftman_id',
            $craftman->id
        )
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingVerification) {

            return redirect()
                ->route('herafy.verification.show')
                ->with('error', 'لديك بالفعل طلب توثيق قيد المراجعة أو حسابك موثق بالفعل.');
        }


        /*
        |--------------------------------------------------------------------------
        | رفع الوثيقة
        |--------------------------------------------------------------------------
        */

        $path = $request->file('document')->store(
            'verifications',
            'public'
        );


        /*
        |--------------------------------------------------------------------------
        | إنشاء الطلب
        |--------------------------------------------------------------------------
        */

        $verification = Verification::create([
            'craftman_id'   => $craftman->id,
            'document_path' => $path,
            'status'        => 'pending',
        ]);


        return redirect()
            ->route(
                'herafy.verification.show'
            )
            ->with(
                'success',
                'تم إرسال طلب التوثيق بنجاح، وسيتم مراجعته من قبل الإدارة.'
            );
    }


    // عرض حالة توثيق الحرفي
    public function myVerification()
    {
        $craftman = auth()->user()->actor;

        $verification = Verification::where(
            'craftman_id',
            $craftman->id
        )
            ->latest()
            ->first();

        return view(
            'herafy.verification.show',
            compact('verification')
        );
    }
}