@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\GridController@edit', $entry->id) : action('Admin\GridController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Title *', 'title', $entry->title, $errors) !!}
{!! HTMLFormHelper::select('Slider *', 'slider_id', $sliders_array, $entry->slider_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::text('Ordering *', 'ordering', $entry->ordering, $errors) !!}
{!! HTMLFormHelper::file('Image', 'img', $entry->img, $errors, [], '', ['type' => 'image', 'preview' => AssetHelper::file('grid', $entry->id, $entry->img, '200x0')]) !!}
<div style="display:none;">
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
</div>
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
