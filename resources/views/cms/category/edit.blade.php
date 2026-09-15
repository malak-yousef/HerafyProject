@extends('cms.parent')
@section('title','تعديل التصنيف') @section('main_content','تعديل التصنيف')
@section('content')<div class="container-fluid"><div class="mb-4"><h4 class="cms-form-title mb-1">تعديل التصنيف</h4><p class="text-muted mb-0">حدّث بيانات التصنيف.</p></div><div class="card cms-form-card"><div class="card-body"><form method="POST" action="{{ route('categories.update',$categories) }}">@csrf @method('PUT') @include('cms.category._form')</form></div></div></div>@endsection
