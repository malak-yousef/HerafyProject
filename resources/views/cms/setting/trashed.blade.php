@extends('cms.parent')
@section('title','سلة الإعدادات')
@section('content')
<x-cms.trash-page :items="$settings" title="الإعدادات" :back-url="route('settings.index')" :columns="['المفتاح'=>'key','القيمة'=>'value']" restore-route="settings.restore" force-delete-route="settings.forceDelete" permission="settings.manage" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
