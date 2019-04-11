@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
This page enables the Administrator to amend any video uploaded to an individual Instructor.
<br><br>
To amend a video, simply select the relevant instructor row from the list (which can be ordered by Instructor surname) and update the URL.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
