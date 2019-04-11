@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
This page enables the Administrator to view and amend any imagery uploaded to an individual Instructor page. The list can be ordered by clicking on any field heading. Select an image row to edit or delete the image.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
