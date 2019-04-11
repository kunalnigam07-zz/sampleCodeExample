@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\BanController@edit', $entry->id) : action('Admin\BanController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('IP Address', 'ip', $entry->ip, $errors) !!}
{!! HTMLFormHelper::text('Email Address', 'email', $entry->email, $errors) !!}
{!! HTMLFormHelper::text('Bank Account Number', 'bank_account_number', $entry->bank_account_number, $errors) !!}
{!! HTMLFormHelper::text('PayPal Email', 'paypal_email', $entry->paypal_email, $errors) !!}
{!! HTMLFormHelper::textarea('Private Notes', 'notes', $entry->notes, $errors) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
