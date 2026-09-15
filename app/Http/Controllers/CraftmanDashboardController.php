<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Profession;
use App\Models\ServiceJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CraftmanDashboardController extends Controller
{
    public function show(): View
    {
        $craftman = $this->authenticatedCraftman();

        return view('herafy.craftman-dashboard', [
            'craftman' => $craftman,
            'summary' => [
                'availableOrders' => Order::query()->eligibleForCraftman($craftman)->count(),
                'pendingOffers' => Offer::query()
                    ->where('craftman_id', $craftman->id)
                    ->where('status', 'pending')
                    ->count(),
                'activeJobs' => ServiceJob::query()
                    ->where('craftman_id', $craftman->id)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->count(),
                'completedJobs' => ServiceJob::query()
                    ->where('craftman_id', $craftman->id)
                    ->where('status', 'completed')
                    ->count(),
            ],
            'recentOffers' => Offer::query()
                ->where('craftman_id', $craftman->id)
                ->with('order')
                ->latest()
                ->limit(5)
                ->get(),
            'recentJobs' => ServiceJob::query()
                ->where('craftman_id', $craftman->id)
                ->with('order')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    public function profile(): View
    {
        $craftman = $this->authenticatedCraftman();
        $approvedReviews = $craftman->reviews()->where('status', 'approved');

        $craftman->load(['user', 'profession', 'verifications' => fn ($query) => $query->latest()]);

        return view('herafy.craftman-profile', [
            'craftman' => $craftman,
            'verification' => $craftman->verifications->first(),
            'averageRating' => (float) ((clone $approvedReviews)->avg('rating') ?? 0),
            'reviewsCount' => (clone $approvedReviews)->count(),
            'recentReviews' => (clone $approvedReviews)->latest()->limit(5)->get(),
        ]);
    }

    public function editProfile(): View
    {
        $craftman = $this->authenticatedCraftman()->load(['user', 'profession']);

        return view('herafy.craftman-profile-edit', [
            'craftman' => $craftman,
            'professions' => Profession::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $craftman = $this->authenticatedCraftman();
        $user = $craftman->user;
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'max:45', Rule::unique('users', 'user_name')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'profession_id' => [
                'required',
                Rule::exists('professions', 'id')->where(fn ($query) => $query
                    ->where('status', 'active')->whereNull('deleted_at')),
            ],
            'experience_years' => ['required', 'integer', 'min:0', 'max:255'],
            'hourly_rate' => ['required', 'numeric', 'min:0'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $newPath = null;
        $oldPath = $user->profilePhotoPath();

        try {
            if ($request->hasFile('profile_photo')) {
                $newPath = $request->file('profile_photo')
                    ->store("profile-photos/craftmen/{$craftman->id}", 'public');
            }

            DB::transaction(function () use ($craftman, $user, $validated, $newPath): void {
                $user->update([
                    'name' => $validated['name'],
                    'user_name' => $validated['user_name'],
                    'email' => $validated['email'],
                    'mobile' => $validated['mobile'],
                    'address' => $validated['address'],
                    'gender' => $validated['gender'],
                    ...($newPath ? ['image' => $newPath] : []),
                ]);
                $craftman->update([
                    'profession_id' => $validated['profession_id'],
                    'experience_years' => $validated['experience_years'],
                    'hourly_rate' => $validated['hourly_rate'],
                    'daily_rate' => $validated['daily_rate'],
                    'description' => $validated['description'] ?? null,
                ]);
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            Log::error('Craftman profile update failed.', ['craftman_id' => $craftman->id, 'exception' => $exception]);

            return back()->withInput()->with('error', 'تعذر تحديث الملف الشخصي. يرجى المحاولة مرة أخرى.');
        }

        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('herafy.craftman.profile.show')->with('success', 'تم تحديث الملف الشخصي بنجاح.');
    }

    private function authenticatedCraftman(): Craftman
    {
        $user = auth('craftman')->user();
        $craftman = $user?->actor;

        abort_unless($user?->actor_type === Craftman::class && $craftman instanceof Craftman, 403);

        return $craftman;
    }
}
