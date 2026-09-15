@extends('cms.parent')
@section('title','سلة العروض')
@section('content')
<x-cms.trash-page :items="$offers" title="العروض" :back-url="route('offers.index')" :columns="['الطلب'=>'order.title','الحرفي'=>'craftman.user.name','السعر'=>'price','الحالة'=>'status']" restore-route="offers_restore" force-delete-route="offers_force" permission="offers.delete" :bulk-force-delete-url="route('offers_forceAll')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
