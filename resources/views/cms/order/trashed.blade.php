@extends('cms.parent')
@section('title','سلة الطلبات')
@section('content')
<x-cms.trash-page :items="$orders" title="الطلبات" :back-url="route('orders.index')" :columns="['الطلب'=>'title','العميل'=>'customer.user.name','المهنة'=>'profession.name','المدينة'=>'city.name','الحالة'=>'status']" restore-route="orders_restore" force-delete-route="orders_force" permission="orders.delete" :bulk-force-delete-url="route('orders_forceAll')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
