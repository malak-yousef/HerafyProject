@extends('cms.parent')
@section('title','سلة الأعمال الخدمية')
@section('content')
<x-cms.trash-page :items="$jobs" title="الأعمال الخدمية" :back-url="route('service-jobs.index')" :columns="['الطلب'=>'order.title','العميل'=>'customer.user.name','الحرفي'=>'craftman.user.name','الحالة'=>'status']" restore-route="service-jobs.restore" force-delete-route="service-jobs.force-delete" permission="service-jobs.delete" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
