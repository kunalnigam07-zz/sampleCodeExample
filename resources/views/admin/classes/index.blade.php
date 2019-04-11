@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
Theses pages enable the Administrator to view and amend details of all classes input by individual instructors. All class times appear in GMT.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
