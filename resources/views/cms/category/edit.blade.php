@extends('cms.parent')

@section('title', 'تعديل تصنيفات')

@section('styles' )

<style>
    .categories-page {
        padding: 30px;
    }

    .category-card {
        background: white;
        padding: 25px;
        border-radius: 18px;
        box-shadow: 0 5px 25px #00000010;
    }


    .add-btn {

        background: #ffbf00;
        color: #14213d;
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: bold;

    }
</style>

@endsection

@section('main_content')

تعديل تصنيفات

@endsection


@section('content')

<div class="main-content">
    <div class="row">
        <div class="col-xl-12">
            <div class="card invoice-container">

                <form id="category-form">

                    <div class="card-header">
                        <h5>تعديل تصنيف</h5>
                        <div class="page-header-right ms-auto">
                            <div class="page-header-right-items">
                                <div class="d-flex d-md-none">
                                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                        <i class="feather-arrow-left me-2"></i>
                                        <span>Back</span>
                                    </a>
                                </div>
                                <div class="buttons">
                                    <button class="btn btn-warning" type="button" onclick="performUpdate({{ $categories->id }})">Update</button>
                                    <a href="{{ route('categories.index') }}" class="btn btn-info" type="button">Go Back</a>
                                </div>
                            </div>
                            <div class="d-md-none d-flex align-items-center">
                                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                                    <i class="feather-align-right fs-20"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="px-4 pt-4">
                            <div class="row align-items-center">


                                <div class="col-md-9">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label" for="name">ادخل اسم التصنيف</label>
                                                <input id="name" name="name" class="form-control" placeholder=" تعديل اسم التصنيف..." value="{{ $categories->name }}">

                                            </div>
                                        </div>




                                    </div>
                                </div>

                            </div>
                        </div>
                        <hr class="border-dashed">




                        <div class="px-4 row justify-content-between">
                            <div class="form-group row">
                                <label for="description" class="col-sm-3 col-form-label">تعديل وصف التصنيف</label>
                                <div class="col-sm-9">

                                    <textarea rows="5" class="form-control" id="description" name="description" placeholder="ادخل وصفك">{{ old('description', $categories->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="border-dashed">
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')



<script>
    function performStore() {
        const form = document.getElementById('category-form');
        let formData = new FormData(form);

        // إرسال البيانات
        // store('/cms/admin/deanships', formData , 'deanship-form');
        store('/cms/admin/categories', formData);
    }
</script>
<script>
    function performUpdate(id) {

        let formData = new FormData();
        formData.append('name', document.getElementById('name').value);
        formData.append('description', document.getElementById('description').value);
        // إرسال البيانات

        storeRoute('/cms/admin/categories_update/' + id, formData);
    }
</script>

@endsection