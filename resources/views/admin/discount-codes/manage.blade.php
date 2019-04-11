@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\DiscountCodeController@edit', $entry->id) : action('Admin\DiscountCodeController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Title *', 'title', $entry->title, $errors, [], 'The code description displayed to the member.') !!}
{!! HTMLFormHelper::text('Discount Code *', 'code', $entry->code, $errors, [], 'This is an automated code and should not need to be amended. It has to be unique.') !!}
{!! HTMLFormHelper::radiogroup('Type', 'type', [1 => 'Free Use', 2 => 'Cancellation'], $entry->type, $errors, [], '‘Free use’ or ‘Cancellation’ depicts the reason for the code creation (a code is automatically generated if an instructor doesn’t start a session within 10 mins of the allotted start time.') !!}
{!! HTMLFormHelper::select('Only for Instructor', 'instructor_id', [0 => 'Any Instructor'] + $instructors_array, $entry->instructor_id, $errors, ['class' => 'chosen-select'], 'Enables code redemption to be restricted to a specified Instructor.') !!}
{!! HTMLFormHelper::select('Only for Member', 'user_id', [0 => 'Anybody'] + $members_array, $entry->user_id, $errors, ['class' => 'chosen-select'], 'Enables the code to be used by specific members or ‘Anybody’.') !!}
{!! HTMLFormHelper::text('Email Restriction', 'email', $entry->email, $errors, [], 'Restrict use to users with a specific email address or email domain. Examples: <b>user@example.com</b> or <b>@example.com</b> (remember to include the @ symbol).') !!}

{!! AuthHelper::isWL() ? '<div style="display:none;">' : '' !!}
{!! HTMLFormHelper::text('Class Max Members', 'class_max_number', $entry->class_max_number, $errors, [], 'Determines whether code can be used for ' . ClassHelper::bulkType(0) . ' or ' . ClassHelper::bulkType(1) . ' classes - only applicable to cancellation codes. This field won\'t need to be amended by the Administrator.') !!}
{!! AuthHelper::isWL() ? '</div>' : '' !!}

{!! HTMLFormHelper::text('Start Date *', 'starts_at', DateHelper::showDate($entry->starts_at), $errors, ['data-date-format' => 'YYYY-MM-DD H:mm:ss'], 'Depicts the period during which the redemption code is valid.', ['type' => 'date', 'data' => '']) !!}
{!! HTMLFormHelper::text('End Date *', 'ends_at', DateHelper::showDate($entry->ends_at), $errors, ['data-date-format' => 'YYYY-MM-DD H:mm:ss'], 'Depicts the period during which the redemption code is valid.', ['type' => 'date', 'data' => '']) !!}
{!! HTMLFormHelper::textarea('Private Notes', 'notes', $entry->notes, $errors) !!}

@if (!isset($entry->id))
    {!! HTMLFormHelper::text('Number of Codes *', 'num_codes', 1, $errors, [], 'Number of codes to generate. If more than 1, the Discount Code entered above is ignored and unique codes are generated automatically.') !!}
@endif

{!! AuthHelper::isWL() ? '<div style="display:none;">' : '' !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! AuthHelper::isWL() ? '</div>' : '' !!}

{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
