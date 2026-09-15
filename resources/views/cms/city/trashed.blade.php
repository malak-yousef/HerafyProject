@extends('cms.parent')
@section('title','سلة المدن')
@section('content')
<x-cms.trash-page :items="$cities" title="المدن" :back-url="route('cities.index')" :columns="['المدينة'=>'name']" restore-route="cities_restore" force-delete-route="cities_force" permission="cities.delete" :bulk-force-delete-url="route('cities_forceAll')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
