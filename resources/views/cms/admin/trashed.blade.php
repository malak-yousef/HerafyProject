@extends('cms.parent')
@section('title','سلة المشرفين')
@section('content')
<x-cms.trash-page :items="$admins" title="المشرفون" :back-url="route('admins.index')" :columns="['الاسم'=>'name','اسم المستخدم'=>'user_name','البريد الإلكتروني'=>'email','الحالة'=>'status']" restore-route="admins_restore" force-delete-route="admins_force" permission="admins.delete" :bulk-force-delete-url="route('admins_forceAll')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
