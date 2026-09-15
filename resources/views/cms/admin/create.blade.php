@extends('cms.parent')

@section('title', 'إضافة مسؤول')
@section('main_content', 'إضافة مسؤول')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fas fa-user-plus text-warning" aria-hidden="true"></i> إضافة مسؤول</h4>
        <p class="text-muted mb-0">أدخل بيانات الحساب وحدد الدور الإداري المسموح.</p>
    </div>
    <div class="card cms-form-card">
        <div class="card-body">
            <form action="{{ route('admins.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('cms.admin._form')
            </form>
        </div>
    </div>
</div>
@endsection
