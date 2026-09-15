@extends('cms.parent')
@section('title','سلة المهن')
@section('content')
<x-cms.trash-page :items="$professions" title="المهن" :back-url="route('professions.index')" :columns="['المهنة'=>'name','التصنيف'=>'category.name','الحالة'=>'status']" restore-route="professions_restore" force-delete-route="professions_force" permission="professions.delete" :bulk-force-delete-url="route('professions_forceAll')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
