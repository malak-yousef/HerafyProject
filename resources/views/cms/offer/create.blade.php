@extends('cms.parent')

@section('title', 'إضافة عرض')
@section('main_content', 'إضافة عرض')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fas fa-hand-holding-dollar text-warning" aria-hidden="true"></i> إضافة عرض جديد</h4>
        <p class="text-muted mb-0">أدخل بيانات العرض واربطه بالطلب والحرفي المناسبين.</p>
    </div>
    <div class="card cms-form-card"><div class="card-body">
        <form action="{{ route('offers.store') }}" method="POST">
            @csrf
            @include('cms.offer._form')
        </form>
    </div></div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('cms/js/marketplace-relations.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', function () { CmsMarketplaceRelations.initOffer('#offer-order', '#offer-craftman'); });</script>
@endsection
