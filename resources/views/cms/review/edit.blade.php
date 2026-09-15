@extends('cms.parent')

@section('title', 'تعديل التقييم')
@section('main_content', 'تعديل التقييم')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fa-solid fa-pen-to-square text-warning" aria-hidden="true"></i> تعديل التقييم</h4>
        <p class="text-muted mb-0">راجع التقييم وتحكم بظهوره دون تعديل رأي العميل أو عدد النجوم.</p>
    </div>

    <div class="card cms-form-card">
        <div class="card-body">
            <form action="{{ route('reviews.update', $review) }}" method="POST">
                @csrf
                @method('PUT')
                @include('cms.review._form')
            </form>
        </div>
    </div>
</div>
@endsection
