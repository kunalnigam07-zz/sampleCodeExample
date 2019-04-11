@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ action('Admin\BookingController@export') }}" enctype="multipart/form-data">
<div class="inner clearfix">

{!! HTMLFormHelper::legend('Export') !!}

<p class="dox-detail-heading">Enables the Administrator to export (download) the information into an Excel document.</p>

{!! HTMLFormHelper::text('Start Date', 'start', '', $errors, ['data-date-format' => 'YYYY-MM-DD'], 'The start date (included) of the records you wish to export. Leave blank to export all.', ['type' => 'date', 'data' => '']) !!}
{!! HTMLFormHelper::submit('Export', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
