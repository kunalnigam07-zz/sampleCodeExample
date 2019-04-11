@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
A list is a collection of people that an instructor can invite to sessions through the platform. This function enables the Administrator to add, change or delete individuals (platform members or not) from class invitee lists compiled by individual Instructors. There is also an option to import multiple individuals into a list by copying and pasting from external CRM systems (to import, click on a list below, then see the section at the bottom of the screen titled ‘import users.’ <br><br>
The rows below show the instructors that have created lists, the list title (name), when the list was created and whether the list is active (visible and able to be used by the instructor).
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
