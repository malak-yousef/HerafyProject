@extends('cms.parent')

@section('title', 'تعديل العرض')
@section('main_content', 'تعديل العرض')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fas fa-pen-to-square text-warning" aria-hidden="true"></i> تعديل العرض</h4>
        <p class="text-muted mb-0">راجع بيانات العرض مع الحفاظ على توافق الطلب والحرفي والحالة.</p>
    </div>
    <div class="card cms-form-card"><div class="card-body">
        <form action="{{ route('offers.update', $offer) }}" method="POST">
            @csrf
            @method('PUT')
            @include('cms.offer._form')
        </form>
    </div></div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('cms/js/marketplace-relations.js') }}"></script>
<script>document.addEventListener('DOMContentLoaded', function () { CmsMarketplaceRelations.initOffer('#offer-order', '#offer-craftman'); });</script>
@endsection
