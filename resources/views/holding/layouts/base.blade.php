<!DOCTYPE html>
<html>
<head>
@include('holding.partials.head')
@section('extra_head')
@show
</head>
@include('holding.partials.body')
@include('holding.partials.header')
@yield('body')
@include('holding.partials.footer')
@section('extra_footer')
@show
</body>
</html>
