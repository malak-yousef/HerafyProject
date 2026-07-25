@extends('cms.parent')

@section('title', 'اضافة مهن')

@section('styles' )

<style>
    .profession-page {
        padding: 30px;
    }

    .profession-card {
        background: #fff;
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .08);
    }

    .page-title {
        color: #14213d;
        font-weight: bold;
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #14213d;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #ddd;
        padding: 12px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #ffbf00;
        box-shadow: 0 0 0 .2rem rgba(255, 191, 0, .25);
    }

    textarea {
        resize: none;
    }

    .save-btn {
        background: #ffbf00;
        color: #14213d;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: bold;
        transition: .3s;
    }

    .save-btn:hover {
        background: #e6aa00;
    }

    .cancel-btn {
        background: #14213d;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        margin-left: 10px;
        transition: .3s;
    }

    .cancel-btn:hover {
        background: #0d1726;
    }
</style>


@endsection


@section('main_content')

اضافة مهن

@endsection

@section('content')
<div class="container profession-page">

    <div class="profession-card">

        <h3 class="page-title">
            إضافة مهنة جديدة
        </h3>

        <form>

            <div class="row">

                <!-- اسم المهنة -->
                <div class="col-md-6 mb-4">
                    <label class="form-label" for="name">
                        اسم المهنة
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        class="form-control"
                        placeholder="أدخل اسم المهنة">
                </div>

                <!-- التصنيف -->
                <div class="col-md-6 mb-4">

                    <label class="form-label" for="category_id">
                        اسم التصنيف
                    </label>

                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">اختر التصنيف</option>
                        {{-- <option>النجارة</option>
                        <option>السباكة</option>
                        <option>الحدادة</option>
                        <option>الكهرباء</option> --}}
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach

                    </select>

                </div>

            </div>

            <!-- الوصف -->
            <div class="mb-4">

                <label class="form-label" for="description">
                    الوصف
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="form-control"
                    placeholder="اكتب وصفاً للمهنة"></textarea>

            </div>

            <!-- الحالة -->
            <div class="mb-4">
                <label class="form-label" for="status">
                    الحالة
                </label>
                <select id="status" name="status" class="form-select">
                    <option value="active">فعال</option>
                    <option value="in_active">غير فعال</option>
                </select>
            </div>


            <button type="button" onclick="performStore()" class="btn save-btn ">store</button>

            <a href="{{ route('professions.index') }}" class="btn cancel-btn">
                <i class="feather-rotate-ccw me-2"></i>
                <span>Go Back</span>
            </a>

        </form>

    </div>

</div>
@endsection

@section('scripts')


<script>
    function performStore() {

        let formData = new FormData();
        formData.append('name', document.getElementById('name').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('status', document.getElementById('status').value);
        formData.append('category_id', document.getElementById('category_id').value);


        // إرسال البيانات

        store('/cms/admin/professions', formData);
    }
</script>



@endsection