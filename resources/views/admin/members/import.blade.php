@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ action('Admin\MemberController@import') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::legend('Import CSV of Members') !!}
{!! HTMLFormHelper::file('CSV File *', 'csv', '', $errors, [], 'You may download the CSV template <a href="/static/admin/templates/members.csv" style="color:#E67373;" target="_blank">here</a>.', ['type' => 'image', 'preview' => '']) !!}
{!! HTMLFormHelper::radiogroup('Date Format', 'date_format', [1 => '06/21/2016', 2 => '21/06/2016', 3 => '2016-06-21'], 1, $errors) !!}
{!! HTMLFormHelper::select('Country *', 'country', $countries_array, 218, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::select('Timezone *', 'timezone', $timezones_array, 'Europe/London', $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::radiogroup('Pre-Authenticated', 'pre_authenticated', [1 => 'Yes', 0 => 'No'], 1, $errors, [], 'Whether user is set as pre-authenticated. Affected by Feature Settings for Free Classes.') !!}
{!! HTMLFormHelper::submit('Import CSV', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
