@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\InstructorVideoController@edit', $entry->id) : action('Admin\InstructorVideoController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Video URL *', 'url', $entry->url, $errors) !!}
{!! HTMLFormHelper::select('Instructor *', 'user_id', $instructors_array, $entry->user_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::text('Ordering *', 'ordering', $entry->ordering, $errors, [], 'This value sets the order within which the video is displayed, amongst the other videos selected for that instructor. Videos are displayed on the instructor profile in increasing size order, smallest first. The number value can be any positive integer. E.g. 100 will be displayed before 200 etc.') !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors, [], '‘Inactive’ will make the video invisible to both instructor and member.') !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
