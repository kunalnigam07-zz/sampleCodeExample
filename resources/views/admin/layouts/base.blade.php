<!DOCTYPE html>
<html>
<head>
@include('admin.partials.head')
@section('extra_head')
@show
</head>
<body>
@include('admin.partials.header')
<div id="wrapper" class="clearfix expand">
@include('admin.partials.menu')
<div id="content" class="right">
@include('admin.partials.breadcrumbs')
@include('admin.partials.alert')
@yield('body')
</div>
@include('admin.partials.footer')
</div>
@include('admin.partials.footer-scripts')
@section('extra_footer')
@show
</body>
</html>
