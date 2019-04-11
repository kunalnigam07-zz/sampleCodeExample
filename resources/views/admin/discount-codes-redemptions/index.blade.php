@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
This page lists all the redeemed codes and the date they were redeemed.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
