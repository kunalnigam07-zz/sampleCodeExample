@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
This section gives the Administrator an opportunity to view, amend and create messaging that members and instructors will see – messaging relating to their account will be displayed in their ‘my notifications’ tab, with messaging relating to general news being displayed in their ‘newsfeed’ tab. <br><br>
Notifications and news alerts can be targeted to Members and / or Instructors, as specified in the ‘users’ column. <br><br>
Members/Instructors will be alerted to the number of unread notifications by , letting them know that a Notification is available to view.<br><br>
To add a new Notification or Newsfeed, the Administrator simply needs to click on the ‘Create New’ icon in the top righthand corner of the page and insert details as instructed.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
