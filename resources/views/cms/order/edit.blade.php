@extends('cms.parent')

@section('title', 'تعديل الطلب')
@section('main_content', 'تعديل الطلب')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fa-solid fa-pen-to-square text-warning" aria-hidden="true"></i> تعديل الطلب</h4>
        <p class="text-muted mb-0">حدّث بيانات الطلب وموقع تنفيذ الخدمة.</p>
    </div>
    <div class="card cms-form-card"><div class="card-body">
        <form action="{{ route('orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')
            @include('cms.order._form')
        </form>
    </div></div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('cms/js/order-location.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', function () { initCmsOrderLocation('#cms-order-city', '#cms-order-area'); });</script>
@endsection
