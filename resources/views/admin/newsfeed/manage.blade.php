@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\NewsfeedController@edit', $entry->id) : action('Admin\NewsfeedController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::radiogroup('Section', 'section_id', [1 => 'Newsfeed', 2 => 'Notifications'], $entry->section_id, $errors) !!}
{!! HTMLFormHelper::radiogroup('For Users', 'for_users', [0 => 'Both', 2 => 'Instructors Only', 3 => 'Member Only'], $entry->for_users, $errors) !!}
{!! HTMLFormHelper::textarea('Message *', 'contents', $entry->contents, $errors) !!}
{!! HTMLFormHelper::text('URL', 'url', $entry->url, $errors) !!}
{!! HTMLFormHelper::text('Ordering *', 'ordering', $entry->ordering, $errors) !!}
{!! HTMLFormHelper::file('Image', 'img', $entry->img, $errors, [], '', ['type' => 'image', 'preview' => AssetHelper::file('newsfeed', $entry->id, $entry->img, '200x0')]) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
