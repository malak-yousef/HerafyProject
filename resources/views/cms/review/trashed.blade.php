@extends('cms.parent')
@section('title','سلة التقييمات')
@section('content')
<x-cms.trash-page :items="$reviews" title="التقييمات" :back-url="route('reviews.index')" :columns="['الطلب'=>'request.title','العميل'=>'customer.user.name','الحرفي'=>'craftsman.user.name','التقييم'=>'rating','الحالة'=>'status']" restore-route="reviews_restore" force-delete-route="reviews_force" permission="reviews.delete" :bulk-force-delete-url="route('reviews_forceAll')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
