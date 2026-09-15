@extends('herafy.parent')

@section('title', 'تعديل الملف الشخصي')

@section('style')
<style>
    .profile-edit-page { background: #f8f9fa; min-height: 65vh; }
    .profile-edit-card { border: 0; border-radius: 20px; }
    .profile-preview { width: 115px; height: 115px; border-radius: 50%; object-fit: cover; border: 4px solid #fff3cd; }
    .profile-preview-fallback { width: 115px; height: 115px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #fff3cd; color: #8a6200; font-size: 2.5rem; }
</style>
@endsection

@section('content')
@php
    $user = $craftman->user;
    $photoUrl = $user->profilePhotoUrl();
@endphp
<main class="profile-edit-page py-5">
    <div class="container">
        <div class="card profile-edit-card shadow-sm mx-auto" style="max-width: 960px">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div><h1 class="h3 fw-bold mb-1">تعديل الملف الشخصي</h1><p class="text-muted mb-0">حدّث معلوماتك الشخصية والمهنية.</p></div>
                    <a href="{{ route('herafy.craftman.profile.show') }}" class="btn btn-outline-secondary">رجوع</a>
                </div>

                @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

                <form method="POST" action="{{ route('herafy.craftman.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="text-center mb-4">
                        @if($photoUrl)<img src="{{ $photoUrl }}" alt="" class="profile-preview mb-3">@else<span class="profile-preview-fallback mb-3"><i class="fas fa-user"></i></span>@endif
                        <div class="mx-auto" style="max-width: 420px"><label class="form-label" for="profile_photo">صورة الملف الشخصي</label><input class="form-control" type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp"><div class="form-text">JPG أو PNG أو WebP، بحد أقصى 4 ميجابايت.</div></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">الاسم</label><input class="form-control" name="name" value="{{ old('name', $user->name) }}" required></div>
                        <div class="col-md-6"><label class="form-label">اسم المستخدم</label><input class="form-control" name="user_name" value="{{ old('user_name', $user->user_name) }}" required></div>
                        <div class="col-md-6"><label class="form-label">البريد الإلكتروني</label><input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
                        <div class="col-md-6"><label class="form-label">رقم الهاتف</label><input class="form-control" name="mobile" value="{{ old('mobile', $user->mobile) }}" required></div>
                        <div class="col-md-8"><label class="form-label">العنوان</label><input class="form-control" name="address" value="{{ old('address', $user->address) }}" required></div>
                        <div class="col-md-4"><label class="form-label">الجنس</label><select class="form-select" name="gender" required><option value="male" @selected(old('gender', $user->gender) === 'male')>ذكر</option><option value="female" @selected(old('gender', $user->gender) === 'female')>أنثى</option></select></div>
                        <div class="col-md-6"><label class="form-label">المهنة</label><select class="form-select" name="profession_id" required>@foreach($professions as $profession)<option value="{{ $profession->id }}" @selected((int) old('profession_id', $craftman->profession_id) === $profession->id)>{{ $profession->name }}</option>@endforeach</select></div>
                        <div class="col-md-6"><label class="form-label">رقم الهوية</label><input class="form-control" value="{{ $craftman->id_number }}" disabled><div class="form-text">لا يمكن تغييره من الملف الشخصي.</div></div>
                        <div class="col-md-4"><label class="form-label">سنوات الخبرة</label><input class="form-control" type="number" min="0" max="255" name="experience_years" value="{{ old('experience_years', $craftman->experience_years) }}" required></div>
                        <div class="col-md-4"><label class="form-label">السعر بالساعة</label><input class="form-control" type="number" min="0" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $craftman->hourly_rate) }}" required></div>
                        <div class="col-md-4"><label class="form-label">السعر باليوم</label><input class="form-control" type="number" min="0" step="0.01" name="daily_rate" value="{{ old('daily_rate', $craftman->daily_rate) }}" required></div>
                        <div class="col-12"><label class="form-label">نبذة مهنية</label><textarea class="form-control" name="description" rows="5" maxlength="2000">{{ old('description', $craftman->description) }}</textarea></div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('herafy.craftman.profile.show') }}" class="btn btn-light">إلغاء</a><button class="btn btn-warning px-4" type="submit"><i class="fas fa-floppy-disk"></i> حفظ التغييرات</button></div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
