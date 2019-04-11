@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\InstructorListController@edit', $entry->id) : action('Admin\InstructorListController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('List Title *', 'title', $entry->title, $errors) !!}
{!! HTMLFormHelper::select('Instructor *', 'user_id', $instructors_array, $entry->user_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors, [], 'Whether list is visible to/usable by the instructor.') !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>

@if (count($list_members) > 0)
    <form id="file-upload" class="upload" method="post" action="{{ action('Admin\InstructorListController@action', $entry->id) }}" enctype="multipart/form-data">
    <div class="inner clearfix">
    {!! HTMLFormHelper::legend('Users in the List') !!}
    {!! HTMLFormHelper::checkgroup('Users *', 'users', $list_members, [], $errors, [], $overflow = 0, $multiple = false) !!}
    {!! HTMLFormHelper::select('Action *', 'action', [1 => 'Copy Selected Users To...', 2 => 'Move Selected Users To...', 3 => 'Delete Selected Users'], 1, $errors, ['class' => 'chosen-select'], 'Actions are to copy, move or delete users from the list.') !!}
    {!! HTMLFormHelper::select('Target List Title', 'list', $all_lists_array, 0, $errors, ['class' => 'chosen-select'], 'Only required when copying or moving users.') !!}
    {!! HTMLFormHelper::submit('Proceed', ['class' => 'right']) !!}
    </div>
    {!! csrf_field() !!}
    </form>
@endif

@if (isset($entry->id))
    <form id="file-upload" class="upload" method="post" action="{{ action('Admin\InstructorListController@import', $entry->id) }}" enctype="multipart/form-data">
    <div class="inner clearfix">
    {!! HTMLFormHelper::legend('Import Users') !!}

    <p class="dox-detail-heading">This function enables the Administrastor to add multiple multiple individuals by copying and pasting &lt;name&gt; &lt;,&gt; &lt;email address&gt; from an external data source, with each record on a new line, then pressing the ‘import’ button. Data will then be added to the ‘Users in the list’ in the middle of the page.</p>

    {!! HTMLFormHelper::textarea('CSV Data *', 'data', '', $errors, ['placeholder' => 'John,john@example.com']) !!}
    {!! HTMLFormHelper::submit('Import', ['class' => 'right']) !!}
    </div>
    {!! csrf_field() !!}
    </form>
@endif

@stop
