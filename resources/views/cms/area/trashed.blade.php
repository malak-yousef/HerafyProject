@extends('cms.parent')
@section('title','سلة المناطق')
@section('content')
<x-cms.trash-page :items="$areas" title="المناطق" :back-url="route('areas.index')" :columns="['المنطقة'=>'name','المدينة'=>'city.name']" restore-route="areas_restore" force-delete-route="areas_force" permission="areas.delete" :bulk-force-delete-url="route('areas_forceAll')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
