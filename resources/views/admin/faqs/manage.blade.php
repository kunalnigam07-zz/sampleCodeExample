@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\FAQController@edit', $entry->id) : action('Admin\FAQController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Question *', 'question', $entry->question, $errors) !!}
{!! HTMLFormHelper::textarea('Answer *', 'answer', $entry->answer, $errors, ['class' => 'tinymce']) !!}
{!! HTMLFormHelper::select('Category *', 'category_id', $categories_array, $entry->category_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::text('Ordering *', 'ordering', $entry->ordering, $errors) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
@section('extra_footer')
@include('admin.partials.tinymce', ['tinymcetype' => 'normal'])
@stop
