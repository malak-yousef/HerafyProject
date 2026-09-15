@extends('cms.parent')

@section('title', 'إضافة عميل')
@section('main_content', 'إضافة عميل')

@section('content')
<div class="container-fluid">
    <div class="mb-4"><h4 class="cms-form-title mb-1"><i class="fas fa-user-plus text-warning" aria-hidden="true"></i> إضافة عميل</h4><p class="text-muted mb-0">أدخل بيانات الحساب والملف الشخصي للعميل.</p></div>
    <div class="card cms-form-card"><div class="card-body">
        <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('cms.customer._form')
        </form>
    </div></div>
</div>
@endsection
