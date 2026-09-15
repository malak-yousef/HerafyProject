@extends('cms.parent')
@section('title','سلة العملاء')
@section('content')
<x-cms.trash-page :items="$customers" title="العملاء" :back-url="route('customers.index')" :columns="['العميل'=>'user.name','البريد الإلكتروني'=>'user.email','الهاتف'=>'user.mobile']" restore-route="customers_restore" force-delete-route="customers_force" permission="customers.delete" :bulk-force-delete-url="route('customers_forceAll')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
