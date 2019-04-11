@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
This page lists details of all created redemption codes. By clicking on the ‘pen’ icon the Administrator will be given access to the details of each individual code.
<br><br>
At the top right-hand corner there is a ‘Create’ icon. This allows the Administrator to input details of a new redemption code. Lists can also be ‘Exported’ into n Excel document via the ‘Export’ icon.
<br><br>
This page (accessed via the ‘pen’ icon on the Discount Code page, enables the Administrator to view and amend individual created codes.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
