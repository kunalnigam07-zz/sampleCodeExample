@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\BulkPackageController@edit', $entry->id) : action('Admin\BulkPackageController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::select('Instructor *', 'user_id', $instructors_array, $entry->user_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::text('Number of Classes *', 'classes_number', $entry->classes_number, $errors) !!}
{!! HTMLFormHelper::text('Price *', 'price', $entry->price, $errors, [], '', ['type' => 'pre', 'data' => '£']) !!}
{!! HTMLFormHelper::text('Expiry (in days) *', 'expiry_days', $entry->expiry_days, $errors) !!}
{!! HTMLFormHelper::radiogroup('Package Type', 'type', [0 => ClassHelper::bulkType(0), 1 => ClassHelper::bulkType(1)], $entry->type, $errors) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
