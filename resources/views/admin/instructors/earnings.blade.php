@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ action('Admin\InstructorController@earnings') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Month *', 'month', Carbon::parse('last month')->format('Y-m'), $errors, ['data-date-format' => 'YYYY-MM'], 'The month of the records you wish to export.', ['type' => 'date', 'data' => '']) !!}
{!! HTMLFormHelper::submit('Export', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
