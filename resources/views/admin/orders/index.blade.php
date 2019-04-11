@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
These pages provide the Administrator with access to data relating to discount codes awarded to individual Members. The Administrator to able to issue discount codes to one or multiple Members and to view which pre-issued codes have been redeemed.
<br><br>
Clicking on ‘Payments’ displays a list of all members who have been issued with a discount code and reason for the issue e.g. ‘class cancelled’. A date also references when the code was issued.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
