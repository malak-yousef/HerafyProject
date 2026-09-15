@extends('herafy.parent')

@section('title', 'تعديل الملف الشخصي')

@section('style')
<style>
    .profile-edit-page { background: #f8f9fa; min-height: 65vh; }
    .profile-edit-card { border: 0; border-radius: 20px; }
    .profile-preview { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid #fff3cd; }
    .profile-preview-fallback { width: 120px; height: 120px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: #fff3cd; color: #8a6200; font-size: 3rem; }
</style>
@endsection

@section('content')
@php
    $user = $customer->user;
    $photoUrl = $user->profilePhotoUrl();
@endphp
<main class="profile-edit-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 fw-bold mb-0">تعديل الملف الشخصي</h1>
                    <a href="{{ route('herafy.customer.profile.show') }}" class="btn btn-outline-secondary">رجوع</a>
                </div>
                <section class="card profile-edit-card shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <form method="POST" action="{{ route('herafy.customer.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="text-center mb-4">
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="صورة {{ $user->name }}" class="profile-preview mb-3">
                                @else
                                    <span class="profile-preview-fallback mb-3"><i class="fas fa-user"></i></span>
                                @endif
                                <div class="mx-auto" style="max-width: 420px">
                                    <label class="form-label" for="profile_photo">تغيير الصورة الشخصية</label>
                                    <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp">
                                    <div class="form-text">JPG أو PNG أو WebP، بحد أقصى 4 ميجابايت.</div>
                                    @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label" for="name">الاسم</label><input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label class="form-label" for="user_name">اسم المستخدم</label><input class="form-control @error('user_name') is-invalid @enderror" id="user_name" name="user_name" value="{{ old('user_name', $user->user_name) }}" required>@error('user_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label class="form-label" for="email">البريد الإلكتروني</label><input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label class="form-label" for="mobile">رقم الهاتف</label><input class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}" required>@error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-12"><label class="form-label" for="address">العنوان</label><input class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}" required>@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-6"><label class="form-label" for="gender">الجنس</label><select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required><option value="male" @selected(old('gender', $user->gender) === 'male')>ذكر</option><option value="female" @selected(old('gender', $user->gender) === 'female')>أنثى</option></select>@error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('herafy.customer.profile.show') }}" class="btn btn-light">إلغاء</a>
                                <button class="btn btn-warning px-4" type="submit"><i class="fas fa-floppy-disk ms-1"></i> حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>
@endsection
