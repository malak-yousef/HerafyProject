@extends('cms.parent')
@section('title','سلة معارض الأعمال')
@section('content')
<x-cms.trash-page :items="$portfolios" title="معارض الأعمال" :back-url="route('portfolios.index')" :columns="['العنوان'=>'title','الحرفي'=>'craftman.user.name','المهنة'=>'craftman.profession.name','الحالة'=>'status']" restore-route="portfolios_restore" force-delete-route="portfolios_force_destroy" permission="portfolios.delete" :bulk-force-delete-url="route('portfolios_destroy_all')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
