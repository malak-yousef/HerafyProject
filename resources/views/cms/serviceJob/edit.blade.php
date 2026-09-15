@extends('cms.parent')

@section('title', 'تعديل مهمة الخدمة')
@section('main_content', 'تعديل مهمة الخدمة')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fas fa-pen-to-square text-warning" aria-hidden="true"></i> تعديل مهمة الخدمة</h4>
        <p class="text-muted mb-0">راجع العلاقات والحالة والتوقيت مع الحفاظ على اتساق المهمة.</p>
    </div>
    <div class="card cms-form-card"><div class="card-body">
        <form action="{{ route('service-jobs.update', $serviceJob) }}" method="POST">
            @csrf
            @method('PUT')
            @include('cms.serviceJob._form')
        </form>
    </div></div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('cms/js/marketplace-relations.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', function () { CmsMarketplaceRelations.initServiceJob('#service-job-order', '#service-job-offer', '#service-job-customer', '#service-job-craftman'); });</script>
@endsection
