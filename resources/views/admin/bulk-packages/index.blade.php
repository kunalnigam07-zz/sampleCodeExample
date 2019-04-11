@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
This page enables the administrator to view all the bulk payments offered by every individual instructor. <br><br>
By clicking on any row/instructor, or the pencil icon (under ‘Actions’), the bulk package option of an individual Instructor, the administrator will move to a new screen to edit the details within a specific instructor’s bulk package.<br><br>
Every instructor will have 4 bulk packages listed (2 for 1:1 and 2 for group sessions) – A bulk package is inactive  where the instructor hasn’t completed any, or all of the information for that bulk package.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
