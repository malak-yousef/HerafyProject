@extends('cms.parent')

@section('title', 'مراجعة طلب التوثيق')

@section('main_content', 'مراجعة طلب التوثيق')

@section('content')

<div class="container-fluid">

    <div class="mb-4">

        <h4 class="fw-bold">
            <i class="fas fa-user-check text-warning"></i>
            مراجعة طلب التوثيق
        </h4>

        <p class="text-muted">
            مراجعة وثيقة الحرفي وتحديد حالة التوثيق
        </p>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="row g-4">


                {{-- بيانات الحرفي --}}
                <div class="col-md-6">

                    <h5 class="fw-bold mb-3">
                        بيانات الحرفي
                    </h5>

                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            الحرفي
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $verification->craftman->user->name ?? 'غير معروف' }}"
                               disabled>

                    </div>


                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            رقم الحرفي
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $verification->craftman_id }}"
                               disabled>

                    </div>

                </div>


                {{-- الوثيقة --}}
                <div class="col-md-6">

                    <h5 class="fw-bold mb-3">
                        وثيقة التوثيق
                    </h5>

                    <div class="mb-3">

                        <a href="{{ asset('storage/' . $verification->document_path) }}"
                           target="_blank"
                           class="btn btn-primary">

                            <i class="fas fa-file-alt"></i>
                            فتح الوثيقة

                        </a>

                    </div>

                </div>


                {{-- الحالة --}}
                <div class="col-md-12">

                    <form action="{{ route('verifications.update', $verification->id) }}"
                          method="POST">

                        @csrf
                        @method('PUT')


                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                حالة التوثيق
                            </label>

                            <select name="status"
                                    class="form-select"
                                    required>

                                <option value="pending"
                                    {{ $verification->status === 'pending' ? 'selected' : '' }}>
                                    قيد المراجعة
                                </option>

                                <option value="approved"
                                    {{ $verification->status === 'approved' ? 'selected' : '' }}>
                                    مقبول
                                </option>

                                <option value="rejected"
                                    {{ $verification->status === 'rejected' ? 'selected' : '' }}>
                                    مرفوض
                                </option>

                            </select>

                        </div>


                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-success">

                                <i class="fas fa-save"></i>
                                حفظ الحالة

                            </button>


                            <a href="{{ route('verifications.show', $verification->id) }}"
                               class="btn btn-secondary">

                                إلغاء

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection