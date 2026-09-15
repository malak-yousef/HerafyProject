@extends('cms.parent')
@section('title','إضافة تصنيف') @section('main_content','إضافة تصنيف')
@section('content')<div class="container-fluid"><div class="mb-4"><h4 class="cms-form-title mb-1">إضافة تصنيف</h4><p class="text-muted mb-0">أدخل بيانات التصنيف.</p></div><div class="card cms-form-card"><div class="card-body"><form method="POST" action="{{ route('categories.store') }}">@csrf @include('cms.category._form')</form></div></div></div>@endsection
