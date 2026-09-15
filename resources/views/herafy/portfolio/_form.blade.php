<div class="row g-4">
    <div class="col-md-8">
        <label for="portfolio-title" class="form-label fw-bold">عنوان العمل</label>
        <input id="portfolio-title" name="title" value="{{ old('title', $portfolio->title ?? '') }}" maxlength="200" @class(['form-control', 'is-invalid' => $errors->has('title')]) required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="portfolio-date" class="form-label fw-bold">تاريخ التنفيذ</label>
        <input id="portfolio-date" type="date" name="project_date" max="{{ now()->toDateString() }}" value="{{ old('project_date', isset($portfolio) && $portfolio->project_date ? \Illuminate\Support\Carbon::parse($portfolio->project_date)->toDateString() : '') }}" @class(['form-control', 'is-invalid' => $errors->has('project_date')])>
        @error('project_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="portfolio-description" class="form-label fw-bold">وصف العمل</label>
        <textarea id="portfolio-description" name="description" rows="6" maxlength="5000" @class(['form-control', 'is-invalid' => $errors->has('description')]) required>{{ old('description', $portfolio->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="portfolio-location" class="form-label fw-bold">مكان التنفيذ <span class="text-muted fw-normal">(اختياري)</span></label>
        <input id="portfolio-location" name="location" value="{{ old('location', $portfolio->location ?? '') }}" maxlength="255" @class(['form-control', 'is-invalid' => $errors->has('location')])>
        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="portfolio-cover" class="form-label fw-bold">صورة الغلاف @isset($portfolio)<span class="text-muted fw-normal">(اتركها فارغة للإبقاء على الحالية)</span>@endisset</label>
        <input id="portfolio-cover" type="file" name="portfolioCover_image" accept="image/jpeg,image/png,image/webp" @class(['form-control', 'is-invalid' => $errors->has('portfolioCover_image')]) @required(!isset($portfolio))>
        @error('portfolioCover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('herafy.craftman.portfolios.index') }}" class="btn btn-light">إلغاء</a>
    <button type="submit" class="btn btn-warning px-4"><i class="fas fa-floppy-disk ms-1"></i> حفظ العمل</button>
</div>
