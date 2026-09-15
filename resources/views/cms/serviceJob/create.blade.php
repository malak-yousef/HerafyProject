@extends('cms.parent')

@section('title', 'إضافة مهمة خدمة')
@section('main_content', 'إضافة مهمة خدمة')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fas fa-briefcase text-warning" aria-hidden="true"></i> إضافة مهمة خدمة</h4>
        <p class="text-muted mb-0">اربط المهمة بسجلات الطلب والعرض والأطراف المتوافقة.</p>
    </div>
    <div class="card cms-form-card"><div class="card-body">
        <form action="{{ route('service-jobs.store') }}" method="POST">
            @csrf
            @include('cms.serviceJob._form')
        </form>
    </div></div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('cms/js/marketplace-relations.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', function () { CmsMarketplaceRelations.initServiceJob('#service-job-order', '#service-job-offer', '#service-job-customer', '#service-job-craftman'); });</script>
@endsection
