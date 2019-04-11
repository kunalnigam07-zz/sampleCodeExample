@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\UserController@edit', $entry->id) : action('Admin\UserController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::legend('Personal Details') !!}
{!! HTMLFormHelper::text('First Name *', 'name', $entry->name, $errors) !!}
{!! HTMLFormHelper::text('Last Name', 'surname', $entry->surname, $errors) !!}
{!! HTMLFormHelper::text('Email *', 'email', $entry->email, $errors) !!}
{!! HTMLFormHelper::password('Password', 'password', $errors, [], 'Leave blank to retain current password.') !!}

<br>

{!! HTMLFormHelper::legend('Permissions & Access') !!}
{!! HTMLFormHelper::radiogroup('Admin Type', 'admin_type', [1 => 'Full Admin Access', 2 => 'Selected Admin Access'], $entry->admin_type, $errors, ['onchange' => 'if(this.value == 1){$(\'#perm_zone\').hide();}else{$(\'#perm_zone\').show();}']) !!}
{!! $admin_permissions !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
