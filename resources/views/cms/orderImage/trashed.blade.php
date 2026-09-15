@extends('cms.parent')
@section('title','سلة صور الطلبات')
@section('content')
<x-cms.trash-page :items="$order_images" title="صور الطلبات" :back-url="route('order_images.index')" :columns="['الطلب'=>'order.title','مسار الصورة'=>'image_path']" restore-route="order_images_restore" force-delete-route="order_images_force_destroy" permission="order-images.delete" :bulk-force-delete-url="route('order_images_destroy_all')" />
@endsection
@section('scripts')<script src="{{ asset('cms/js/trash-actions.js') }}"></script>@endsection
