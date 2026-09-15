@extends('cms.parent')
@section('title','سلة طلبات التوثيق')
@section('content')
<x-cms.trash-page :items="$verifications" title="طلبات التوثيق" :back-url="route('verifications.index')" :columns="['الحرفي'=>'craftman.user.name','الحالة'=>'status']" restore-route="verifications.restore" :force-delete-route="null" permission="verifications.delete" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
