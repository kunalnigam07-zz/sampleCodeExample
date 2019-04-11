<!DOCTYPE html>
<html>
<head>
@include('admin.partials.head')
@section('extra_head')
@show
</head>
<body class="full">
@yield('body')
@include('admin.partials.footer-scripts')
@section('extra_footer')
@show
</body>
</html>
