@extends('herafy.parent')

@section('title', 'طلب خدمة')

@section('content')
<section class="request-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="request-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="fas fa-pen text-dark"></i>
                        </div>
                        <h3 class="fw-bold">اطلب خدمة الآن</h3>
                        <p class="text-muted">أدخل تفاصيل طلبك ليظهر للحرفيين المختصين.</p>
                    </div>

                    <form method="POST" action="{{ route('herafy.customer.orders.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="profession_id" class="form-label fw-bold">المهنة المطلوبة</label>
                            <select class="form-select form-select-lg @error('profession_id') is-invalid @enderror" id="profession_id" name="profession_id" required @error('profession_id') aria-invalid="true" aria-describedby="profession_id-error" @enderror>
                                <option value="">اختر المهنة</option>
                                @foreach ($professions as $profession)
                                    <option value="{{ $profession->id }}" @selected((int) old('profession_id', request('profession_id')) === $profession->id)>
                                        {{ $profession->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('profession_id')<div id="profession_id-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city_id" class="form-label fw-bold">المدينة</label>
                                <select class="form-select form-select-lg @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required @error('city_id') aria-invalid="true" aria-describedby="city_id-error" @enderror>
                                    <option value="">اختر المدينة</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id')<div id="city_id-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="area_id" class="form-label fw-bold">المنطقة</label>
                                <select class="form-select form-select-lg @error('area_id') is-invalid @enderror" id="area_id" name="area_id" required @error('area_id') aria-invalid="true" aria-describedby="area_id-error" @enderror>
                                    <option value="">اختر المنطقة</option>
                                    @foreach ($cities as $city)
                                        @foreach ($city->areas as $area)
                                            <option value="{{ $area->id }}" data-city="{{ $city->id }}"
                                                @selected(old('area_id') == $area->id)>
                                                {{ $area->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                                @error('area_id')<div id="area_id-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">عنوان الطلب</label>
                            <input class="form-control form-control-lg @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" maxlength="255" required @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
                            @error('title')<div id="title-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">تفاصيل الطلب</label>
                            <textarea class="form-control form-control-lg @error('description') is-invalid @enderror" id="description" name="description" rows="5" required @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description') }}</textarea>
                            @error('description')<div id="description-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="budget" class="form-label fw-bold">الميزانية المتوقعة</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-lg @error('budget') is-invalid @enderror" id="budget" name="budget" value="{{ old('budget') }}" @error('budget') aria-invalid="true" aria-describedby="budget-error" @enderror>
                                @error('budget')<div id="budget-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="service_date" class="form-label fw-bold">تاريخ الخدمة</label>
                                <input type="date" min="{{ now()->toDateString() }}" class="form-control form-control-lg @error('service_date') is-invalid @enderror" id="service_date" name="service_date" value="{{ old('service_date') }}" @error('service_date') aria-invalid="true" aria-describedby="service_date-error" @enderror>
                                @error('service_date')<div id="service_date-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label fw-bold">العنوان التفصيلي</label>
                                <input class="form-control form-control-lg @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" maxlength="255" @error('address') aria-invalid="true" aria-describedby="address-error" @enderror>
                                @error('address')<div id="address-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-bold">رقم الهاتف</label>
                                <input class="form-control form-control-lg @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" maxlength="20" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
                                @error('phone')<div id="phone-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label fw-bold">صور الطلب (اختياري)</label>
                            @php($imagesInvalid = $errors->has('images') || $errors->has('images.*'))
                            <input type="file" class="form-control form-control-lg @class(['is-invalid' => $imagesInvalid])" id="images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple @if($imagesInvalid) aria-invalid="true" aria-describedby="images-error" @endif>
                            <small class="text-muted">حتى 5 صور، وبحد أقصى 4 ميجابايت للصورة.</small>
                            @error('images')<div id="images-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                            @error('images.*')<div id="images-error" class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold">
                            <i class="fas fa-paper-plane"></i> إرسال الطلب
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const city = document.getElementById('city_id');
        const area = document.getElementById('area_id');

        const filterAreas = () => {
            const selectedArea = area.value;

            Array.from(area.options).forEach((option, index) => {
                if (index === 0) return;
                option.hidden = option.dataset.city !== city.value;
            });

            if (!Array.from(area.options).some(option => option.value === selectedArea && !option.hidden)) {
                area.value = '';
            }
        };

        city.addEventListener('change', filterAreas);
        filterAreas();
    });
</script>
@endsection
