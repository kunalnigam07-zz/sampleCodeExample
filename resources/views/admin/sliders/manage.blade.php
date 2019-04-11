@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\SliderController@edit', $entry->id) : action('Admin\SliderController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Title *', 'title', $entry->title, $errors) !!}
{!! HTMLFormHelper::text('Ordering *', 'ordering', $entry->ordering, $errors) !!}
{!! HTMLFormHelper::file('Image Left *', 'img_left', $entry->img_left, $errors, [], '', ['type' => 'image', 'preview' => AssetHelper::file('sliders', $entry->id, $entry->img_left, '200x0')]) !!}
{!! HTMLFormHelper::file('Image Right', 'img_right', $entry->img_right, $errors, [], 'If no RHS image is supplied, the 9-image grid will be displayed instead.', ['type' => 'image', 'preview' => AssetHelper::file('sliders', $entry->id, $entry->img_right, '200x0')]) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
