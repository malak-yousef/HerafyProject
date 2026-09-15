@extends('cms.parent')

@section('title', 'إضافة حرفي')
@section('main_content', 'إضافة حرفي')

@section('content')
<div class="container-fluid">
    <div class="mb-4"><h4 class="cms-form-title mb-1"><i class="fas fa-user-plus text-warning" aria-hidden="true"></i> إضافة حرفي</h4><p class="text-muted mb-0">أدخل بيانات الحساب والبيانات المهنية للحرفي.</p></div>
    <div class="card cms-form-card"><div class="card-body">
        <form action="{{ route('craftmen.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('cms.craftmen._form')
        </form>
    </div></div>
</div>
@endsection
