@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
This page displays a complete list of all class bookings made by individual Members. The list can be ordered in a number of ways by clicking on the various column headings. Once a class has taken place, a member’s individual feedback score can be viewed by clicking on their booking.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
