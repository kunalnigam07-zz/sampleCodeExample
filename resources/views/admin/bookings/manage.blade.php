@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\BookingController@edit', $entry->id) : action('Admin\BookingController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">

@if ($entry->joined_at != null || $entry->refunded_at != null)
    {!! HTMLFormHelper::legend('Information') !!}
    {!! $entry->joined_at != null ? HTMLFormHelper::string('Joined Class', DateHelper::showDate($entry->joined_at)) : '' !!}
    {!! $entry->refunded_at != null ? HTMLFormHelper::string('Refunded On', DateHelper::showDate($entry->refunded_at)) : '' !!}
    <br>
@endif

{!! HTMLFormHelper::legend('Booking Details') !!}
@if (isset($entry->id))
    {!! HTMLFormHelper::string('Class', $entry->classEvent->title . ' - ' . DateHelper::showDate($entry->classEvent->class_at, 'd M \a\t H:i'), '<a href="' . action('Admin\ClassEventController@edit', $entry->class_id) . '">', '</a>') !!}
    {!! HTMLFormHelper::string('Member', $entry->classUser->name . ' ' . $entry->classUser->surname . ' (' . $entry->classUser->email . ')', '<a href="' . RouteHelper::userEditRoute($entry->classUser) . '">', '</a>') !!}
    {!! HTMLFormHelper::hidden('class_id', $entry->class_id) !!}
    {!! HTMLFormHelper::hidden('user_id', $entry->user_id) !!}
@else
    {!! HTMLFormHelper::select('Class *', 'class_id', $classes_array, $entry->class_id, $errors, ['class' => 'chosen-select']) !!}
    {!! HTMLFormHelper::select('Member *', 'user_id', $users_array, $entry->user_id, $errors, ['class' => 'chosen-select']) !!}
@endif

@if (isset($entry->id))
    {!! HTMLFormHelper::text('Member Rating', 'rating', $entry->rating, $errors, [], 'On a scale from 1 - 5.') !!}
    <div style="display:none;">{!! HTMLFormHelper::textarea('Member Comments', 'comments', $entry->comments, $errors) !!}</div>
@endif

<br>

@if (isset($entry->id))
    {!! HTMLFormHelper::legend('Payment Information') !!}
    @if ($entry->order_id > 0)
        {!! HTMLFormHelper::string('Payment Method', 'Order Placed', '<a href="' . action('Admin\OrderController@edit', $entry->order_id) . '">', '</a>') !!}
    @elseif ($entry->discount_code_id > 0)
        {!! HTMLFormHelper::string('Payment Method', 'Discount Code Used', '<a href="' . action('Admin\DiscountCodeController@edit', $entry->discount_code_id) . '">', '</a>') !!}
    @else
        {!! HTMLFormHelper::string('Payment Method', 'None Specified') !!}
    @endif
    <br>
@endif

{!! HTMLFormHelper::legend('Administrative') !!}
{!! HTMLFormHelper::textarea('Private Notes', 'notes', $entry->notes, $errors) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
