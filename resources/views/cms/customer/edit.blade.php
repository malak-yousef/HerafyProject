@extends('cms.parent')

@section('title', 'تعديل العميل')
@section('main_content', 'تعديل العميل')

@section('content')
<div class="container-fluid">
    <div class="mb-4"><h4 class="cms-form-title mb-1"><i class="fas fa-user-pen text-warning" aria-hidden="true"></i> تعديل العميل</h4><p class="text-muted mb-0">حدّث بيانات الحساب والملف الشخصي للعميل.</p></div>
    <div class="card cms-form-card"><div class="card-body">
        <form action="{{ route('customers.update', $customers->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('cms.customer._form')
        </form>
    </div></div>
</div>
@endsection
