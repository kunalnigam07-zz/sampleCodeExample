@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\PageController@edit', $entry->id) : action('Admin\PageController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Page Title *', 'title', $entry->title, $errors) !!}
{!! HTMLFormHelper::text('Page Heading', 'heading', $entry->heading, $errors) !!}
{!! HTMLFormHelper::text('Page Brief', 'brief', $entry->brief, $errors, [], 'Used in the &quot;description&quot; meta tag.') !!}

{!! ($entry->user_added == 0) ? '<div style="display:none;">' : '' !!}
{!! HTMLFormHelper::text('Page URL *', 'url', $entry->url, $errors, [], 'This field needs to be unique and start with /.') !!}
{!! ($entry->user_added == 0) ? '</div>' : '' !!}

{!! HTMLFormHelper::text('Ordering *', 'ordering', $entry->ordering, $errors) !!}
{!! HTMLFormHelper::textarea('Contents', 'contents', $entry->contents, $errors, ['class' => 'tinymce']) !!}
{!! HTMLFormHelper::textarea('Code Inside &lt;head&gt;', 'code_head', $entry->code_head, $errors, [], 'Useful for code snippets that should be included on this page only.') !!}
{!! HTMLFormHelper::textarea('Code Inside Footer', 'code_footer', $entry->code_footer, $errors, [], 'Useful for code snippets that should be included on this page only.') !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
@section('extra_footer')
@include('admin.partials.tinymce', ['tinymcetype' => 'normal'])
@stop
