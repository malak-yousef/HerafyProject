@extends('cms.parent')

@section('title', 'إضافة تقييم')
@section('main_content', 'إضافة تقييم')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fa-solid fa-star text-warning" aria-hidden="true"></i> إضافة تقييم جديد</h4>
        <p class="text-muted mb-0">أدخل بيانات التقييم المرتبط بمهمة خدمة مكتملة.</p>
    </div>

    <div class="card cms-form-card">
        <div class="card-body">
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                @include('cms.review._form')
            </form>
        </div>
    </div>
</div>
@endsection
