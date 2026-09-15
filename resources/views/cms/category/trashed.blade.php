@extends('cms.parent')
@section('title','سلة التصنيفات')
@section('content')
<x-cms.trash-page :items="$categories" title="التصنيفات" :back-url="route('categories.index')" :columns="['التصنيف'=>'name','الوصف'=>'description']" restore-route="categories_restore" force-delete-route="categories_force" permission="categories.delete" :bulk-force-delete-url="route('categories_forceAll')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
