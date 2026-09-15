@extends('cms.parent')
@section('title','سلة الحرفيين')
@section('content')
<x-cms.trash-page :items="$craftmen" title="الحرفيون" :back-url="route('craftmen.index')" :columns="['الحرفي'=>'user.name','اسم النشاط'=>'craft_name','المهنة'=>'profession.name']" restore-route="craftmen_restore" force-delete-route="craftmen_force_destroy" permission="craftmen.delete" :bulk-force-delete-url="route('craftmen_destroy_all')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
