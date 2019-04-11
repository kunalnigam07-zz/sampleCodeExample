@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list" style="text-align:right">
•   <strong>Earnings</strong>. This allow the Administrator download an Excel spreadsheet detailing the individual instructors income and activity from class participants, over any calendar month.<br>
•   <strong>Export</strong>. This enables the Administrator to download details of all instructors’ profile information into an Excel spreadsheet, from any pre-selected start date
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
