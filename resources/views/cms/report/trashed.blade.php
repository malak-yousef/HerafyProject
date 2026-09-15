@extends('cms.parent')
@section('title','سلة البلاغات')
@section('content')
<x-cms.trash-page :items="$reports" title="البلاغات" :back-url="route('reports.index')" :columns="['المبلّغ'=>'reporter.name','المبلّغ عنه'=>'reported.name','السبب'=>'reason']" restore-route="reports.restore" restore-method="PUT" force-delete-route="reports.forceDelete" permission="reports.delete" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
