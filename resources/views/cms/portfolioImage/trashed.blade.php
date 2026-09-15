@extends('cms.parent')
@section('title','سلة صور المعارض')
@section('content')
<x-cms.trash-page :items="$portfolio_images" title="صور معارض الأعمال" :back-url="route('portfolio_images.index')" :columns="['المعرض'=>'portfolio.title','اسم الصورة'=>'image_name','الوصف'=>'image_description']" restore-route="portfolio_images_restore" force-delete-route="portfolio_images_force_destroy" permission="portfolio-images.delete" :bulk-force-delete-url="route('portfolio_images_destroy_all')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
