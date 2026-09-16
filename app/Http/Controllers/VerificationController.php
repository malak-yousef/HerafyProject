<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Verification;
use App\Notifications\VerificationStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    // عرض جميع طلبات التوثيق
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
            'date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'pending_first'])],
        ]);
        $query = Verification::with('craftman.user');
        if ($search = trim($validated['search'] ?? '')) {
            $query->whereHas('craftman.user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('user_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }
        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }
        if (isset($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }
        if (isset($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }
        $sort = $validated['sort'] ?? 'newest';
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'pending_first') {
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")->latest();
        } else {
            $query->latest();
        }
        $verifications = $query->paginate(15)->withQueryString();

        return view('cms.verification.index', compact('verifications'));
    }

    // عرض تفاصيل طلب توثيق للأدمن
    public function show(Verification $verification)
    {
        $verification->load([
            'craftman',
            'craftman.profession',
        ]);

        return view('cms.verification.show', compact('verification'));
    }

    // صفحة مراجعة طلب التوثيق
    public function edit(Verification $verification)
    {
        $verification->load([
            'craftman',
            'craftman.profession',
        ]);

        return view('cms.verification.edit', compact('verification'));
    }

    // تحديث حالة التوثيق
    public function update(Request $request, Verification $verification)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        DB::transaction(function () use ($verification, $validated): void {
            $verification = Verification::query()
                ->lockForUpdate()
                ->findOrFail($verification->id);
            $newStatus = $validated['status'];

            if ($verification->status === $newStatus) {
                return;
            }

            if (in_array($newStatus, ['approved', 'rejected'], true)) {
                $craftman = $verification->craftman()->with('user')->first();

                abort_unless(
                    $craftman
                        && $craftman->user
                        && $craftman->user->actor_type === Craftman::class
                        && $craftman->user->actor_id === $craftman->id,
                    409,
                    'The verification owner is unavailable.'
                );

                DB::afterCommit(fn () => $craftman->user->notify(
                    new VerificationStatusChanged($verification, $newStatus)
                ));
            }

            $verification->update(['status' => $newStatus]);
        });

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
            ->with('craftman.user')
            ->latest('deleted_at')
            ->paginate(15)->withQueryString();

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
        $user = auth('craftman')->user();
        $craftman = $user?->actor;

        abort_unless($user?->actor_type === Craftman::class && $craftman instanceof Craftman, 403);

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

        $user = auth('craftman')->user();
        $craftman = $user?->actor;

        abort_unless($user?->actor_type === Craftman::class && $craftman instanceof Craftman, 403);

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
            'craftman_id' => $craftman->id,
            'document_path' => $path,
            'status' => 'pending',
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
        $user = auth('craftman')->user();
        $craftman = $user?->actor;

        abort_unless($user?->actor_type === Craftman::class && $craftman instanceof Craftman, 403);

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
