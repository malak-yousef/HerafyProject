@extends('cms.parent')

@section('title', 'تعديل الحرفي')
@section('main_content', 'تعديل الحرفي')

@section('content')
<div class="container-fluid">
    <div class="mb-4"><h4 class="cms-form-title mb-1"><i class="fas fa-user-pen text-warning" aria-hidden="true"></i> تعديل الحرفي</h4><p class="text-muted mb-0">حدّث بيانات الحساب والبيانات المهنية للحرفي.</p></div>
    <div class="card cms-form-card"><div class="card-body">
        <form action="{{ route('craftmen.update', $craftmen->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('cms.craftmen._form')
        </form>
    </div></div>
</div>
@endsection
