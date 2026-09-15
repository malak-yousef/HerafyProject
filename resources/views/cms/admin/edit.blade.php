@extends('cms.parent')

@section('title', 'تعديل المسؤول')
@section('main_content', 'تعديل المسؤول')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fas fa-user-pen text-warning" aria-hidden="true"></i> تعديل المسؤول</h4>
        <p class="text-muted mb-0">حدّث بيانات الحساب دون تغيير الأدوار الإدارية.</p>
    </div>
    <div class="card cms-form-card">
        <div class="card-body">
            <form action="{{ route('admins.update', $admins->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('cms.admin._form')
            </form>
        </div>
    </div>
</div>
@endsection
