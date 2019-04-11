@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ action('Admin\EmailTemplateController@edit', $entry->id) }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Email Title *', 'title', $entry->title, $errors) !!}
{!! HTMLFormHelper::text('From Name *', 'from_name', $entry->from_name, $errors) !!}
{!! HTMLFormHelper::text('From Email *', 'from_email', $entry->from_email, $errors) !!}
{!! HTMLFormHelper::text('Subject *', 'subject', $entry->subject, $errors) !!}
{!! HTMLFormHelper::textarea('Text Version *', 'text_version', $entry->text_version, $errors, ['style' => 'height:200px;']) !!}
{!! HTMLFormHelper::textarea('HTML Version *', 'html_version', $entry->html_version, $errors, ['class' => 'tinymce']) !!}

{!! strlen($entry->sms) == 0 ? '<div style="display:none;">' : '' !!}
    {!! HTMLFormHelper::text('SMS Message', 'sms', $entry->sms, $errors, ['maxlength' => 160], 'The message is limited to 160 characters.') !!}
{!! strlen($entry->sms) == 0 ? '</div>' : '' !!}

{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
@section('extra_footer')
@include('admin.partials.tinymce', ['tinymcetype' => 'email'])
@stop
