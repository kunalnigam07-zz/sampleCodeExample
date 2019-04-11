@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\ClassTypeController@edit', $entry->id) : action('Admin\ClassTypeController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Title *', 'title', $entry->title, $errors) !!}
{!! HTMLFormHelper::text('Ordering *', 'ordering', $entry->ordering, $errors) !!}
{!! HTMLFormHelper::select('Parent *', 'parent_id', $parents_array, $entry->parent_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
