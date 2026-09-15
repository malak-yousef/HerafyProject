<x-cms.form-errors-summary class="mb-4" />
<section class="cms-form-section"><h5 class="cms-form-section-title">بيانات المعرض</h5><div class="row g-3"><div class="col-md-6"><label for="portfolio-craftman" class="form-label fw-semibold">الحرفي <span class="cms-required">*</span></label><select id="portfolio-craftman" name="craftman_id" @class(['form-select','is-invalid'=>$errors->has('craftman_id')]) required><option value="">اختر الحرفي</option>@foreach($craftmen as $craftman)<option value="{{ $craftman->id }}" @selected((string)old('craftman_id',$portfolios->craftman_id ?? '')===(string)$craftman->id)>{{ $craftman->user?->name ?? 'غير متاح' }}</option>@endforeach</select><x-cms.form-error field="craftman_id" /></div><div class="col-md-6"><label for="portfolio-title" class="form-label fw-semibold">عنوان المعرض <span class="cms-required">*</span></label><input id="portfolio-title" name="title" maxlength="200" value="{{ old('title',$portfolios->title ?? '') }}" @class(['form-control','is-invalid'=>$errors->has('title')]) required><x-cms.form-error field="title" /></div><div class="col-md-6"><label for="portfolio-location" class="form-label fw-semibold">الموقع <small class="text-muted">(اختياري)</small></label><input id="portfolio-location" name="location" maxlength="255" value="{{ old('location',$portfolios->location ?? '') }}" @class(['form-control','is-invalid'=>$errors->has('location')])><x-cms.form-error field="location" /></div><div class="col-md-6"><label for="portfolio-status" class="form-label fw-semibold">الحالة <span class="cms-required">*</span></label><select id="portfolio-status" name="status" @class(['form-select','is-invalid'=>$errors->has('status')]) required><option value="1" @selected((string)old('status',isset($portfolios)?(int)$portfolios->status:1)==='1')>نشط</option><option value="0" @selected((string)old('status',isset($portfolios)?(int)$portfolios->status:1)==='0')>غير نشط</option></select><x-cms.form-error field="status" /></div><div class="col-12"><label for="portfolio-description" class="form-label fw-semibold">الوصف <span class="cms-required">*</span></label><textarea id="portfolio-description" name="description" rows="5" @class(['form-control','is-invalid'=>$errors->has('description')]) required>{{ old('description',$portfolios->description ?? '') }}</textarea><x-cms.form-error field="description" /></div></div></section>
<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-star text-warning" aria-hidden="true"></i> الظهور في الصفحة الرئيسية</h5>
    <div class="form-check form-switch">
        <input type="hidden" name="is_featured" value="0">
        <input id="portfolio-is-featured" class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $portfolios->is_featured ?? false))>
        <label class="form-check-label fw-semibold" for="portfolio-is-featured">إظهار العمل ضمن معرض الأعمال في الصفحة الرئيسية</label>
    </div>
    <small class="text-muted d-block mt-2">يمكن للمدير اختيار 6 أعمال كحد أقصى، ولن يظهر إلا العمل النشط التابع لحرفي نشط.</small>
    <x-cms.form-error field="is_featured" />
</section>
<section class="cms-form-section">
    <h5 class="cms-form-section-title">صورة الغلاف</h5>
    @isset($portfolios)
        @if($portfolios->portfolioCover_image)
            <div class="cms-profile-image-preview mb-2">
                <img src="{{ asset('storage/images/portfolio/'.$portfolios->portfolioCover_image) }}" alt="صورة الغلاف الحالية">
            </div>
            <p class="text-muted small">تُحفظ الصورة الحالية ما لم تختر صورة بديلة.</p>
        @endif
    @endisset
    <label for="portfolio-cover" class="form-label fw-semibold">صورة الغلاف @isset($portfolios)<small class="text-muted">(اختياري)</small>@else<span class="cms-required" aria-hidden="true">*</span>@endisset</label>
    <input id="portfolio-cover" type="file" name="portfolioCover_image" accept="image/jpeg,image/png,image/gif,image/webp" @class(['form-control','is-invalid'=>$errors->has('portfolioCover_image')]) @unless(isset($portfolios)) required @endunless>
    <x-cms.form-error field="portfolioCover_image" />
</section>
<div class="cms-form-actions mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ isset($portfolios)?'تحديث':'حفظ' }}</button><a href="{{ route('portfolios.index') }}" class="btn btn-outline-secondary">إلغاء</a></div>
