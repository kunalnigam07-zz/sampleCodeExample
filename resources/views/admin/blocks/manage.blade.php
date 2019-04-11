@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\BlockController@edit', $entry->id) : action('Admin\BlockController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Title *', 'title', $entry->title, $errors) !!}

@if ($entry->block_type == 'TEXTBOX')
	{!! HTMLFormHelper::text('Contents *', 'contents', $entry->contents, $errors) !!}
@elseif ($entry->block_type == 'TEXTAREA') 
	{!! HTMLFormHelper::textarea('Contents *', 'contents', $entry->contents, $errors, ['style' => 'height:300px;']) !!}
@elseif ($entry->block_type == 'HTML')
	{!! HTMLFormHelper::textarea('Contents *', 'contents', $entry->contents, $errors, ['class' => 'tinymce']) !!}
@endif

{!! HTMLFormHelper::hidden('page_start_pp', Request::has('start') ? Request::get('start') : 0) !!}

{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
@section('extra_footer')
@include('admin.partials.tinymce', ['tinymcetype' => 'normal'])
@stop
