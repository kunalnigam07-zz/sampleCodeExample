@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
This page enables to Administrator to replace the three generic images which appear on the Home Screen.<br>
•   To remove the generic image, tick the ‘Delete uploaded file?’ box.<br>
•   To replace the image choose a new image using the ‘Browse’ option.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
