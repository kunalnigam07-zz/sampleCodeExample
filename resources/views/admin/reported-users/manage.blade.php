@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\ReportedUserController@edit', $entry->id) : action('Admin\ReportedUserController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::string('Reported By', $entry->reportingMember->name . ' ' . $entry->reportingMember->surname, '<a href="' . RouteHelper::userEditRoute($entry->reportingMember) . '">', '</a>') !!}
{!! HTMLFormHelper::string('Offender', $entry->reportedMember->name . ' ' . $entry->reportedMember->surname, '<a href="' . RouteHelper::userEditRoute($entry->reportedMember) . '">', '</a>') !!}
{!! HTMLFormHelper::string('Reason', $entry->reason) !!}
{!! HTMLFormHelper::textarea('Private Notes', 'notes', $entry->notes, $errors) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Resolved', 0 => 'Unresolved'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
