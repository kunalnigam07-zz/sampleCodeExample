@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\CountryController@edit', $entry->id) : action('Admin\CountryController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Country Name *', 'title', $entry->title, $errors) !!}
{!! HTMLFormHelper::text('Country Code *', 'code', $entry->code, $errors, ['maxlength' => '2']) !!}
{!! HTMLFormHelper::text('Dialing Code *', 'dialing', $entry->dialing, $errors, [], '', ['type' => 'pre', 'data' => '+']) !!}
{!! HTMLFormHelper::radiogroup('Sanctioned?', 'sanctioned', [1 => 'Yes', 0 => 'No'], $entry->sanctioned, $errors) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
