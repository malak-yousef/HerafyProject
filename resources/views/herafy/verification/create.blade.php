@extends('herafy.parent')

@section('title', 'طلب التوثيق')

@section('nav-links')
<!--<li class="nav-item">
    <a class="nav-link active"
       href="{{ route('herafy.verification.show') }}">

        <i class="fas fa-user-check"></i>
        التوثيق

    </a>
</li>-->
@endsection


@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <i class="fas fa-user-check fa-3x text-warning mb-3"></i>

                        <h3 class="fw-bold">
                            طلب توثيق الحساب
                        </h3>

                        <p class="text-muted">
                            قم برفع الوثيقة المطلوبة لإتمام عملية توثيق حسابك.
                        </p>

                    </div>


                    <form action="{{ route('herafy.verification.store') }}"
                          method="POST"
                          enctype="multipart/form-data">

                        @csrf


                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                وثيقة التوثيق
                            </label>

                            <input type="file"
                                   name="document"
                                   class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   required>

                            <div class="form-text">
                                الملفات المسموحة: PDF, JPG, JPEG, PNG
                                <br>
                                الحد الأقصى لحجم الملف: 5MB
                            </div>

                        </div>


                        <button type="submit"
                                class="btn btn-warning w-100 fw-bold">

                            <i class="fas fa-paper-plane"></i>
                            إرسال طلب التوثيق

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection