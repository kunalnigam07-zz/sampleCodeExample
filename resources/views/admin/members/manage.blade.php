@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\MemberController@edit', $entry->id) : action('Admin\MemberController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">

<p class="dox-detail-top">This page enables the administrator to change every detail that appears on a member’s personal profile page.</p>

{!! HTMLFormHelper::legend('Personal Details') !!}

<p class="dox-detail-heading">To make an amend simply type the revised entry into the relevant box.</p>

{!! HTMLFormHelper::text('First Name *', 'name', $entry->name, $errors) !!}
{!! HTMLFormHelper::text('Last Name *', 'surname', $entry->surname, $errors) !!}
{!! HTMLFormHelper::text('Email *', 'email', $entry->email, $errors) !!}
{!! HTMLFormHelper::password('Password', 'password', $errors, [], 'Leave blank to retain current password.') !!}
{!! HTMLFormHelper::file('Profile Image', 'img', $entry->img, $errors, [], 'To change a member’s profile picture simply click on the ‘Profile image’ box and Browse for a suitable replacement, Selected images can be in any recognised picture format (JPEG, PNG, GIF, EPS). The subject matter should be placed centrally to allow for slight cropping around the edges.', ['type' => 'image', 'preview' => AssetHelper::file('members', $entry->id, $entry->img, '200x0')]) !!}
{!! HTMLFormHelper::select('Mobile Country *', 'mobile_country_id', $countries_codes_array, $entry->mobile_country_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::text('Mobile Number', 'mobile', $entry->mobile, $errors) !!}
{!! HTMLFormHelper::text('DOB', 'dob', strlen($entry->dob) > 0 ? DateHelper::showDate($entry->dob) : '', $errors, ['data-date-format' => 'YYYY-MM-DD'], 'Date of Birth - In European format YYYY-MM-DD.', ['type' => 'date', 'data' => '']) !!}
{!! HTMLFormHelper::select('Country *', 'country_id', $countries_array, $entry->country_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::select('Timezone *', 'timezone', $timezones_array, $entry->timezone, $errors, ['class' => 'chosen-select']) !!}

<br>

{!! HTMLFormHelper::legend('Administrative') !!}

@if (isset($entry->id))
    {!! HTMLFormHelper::string('IP Address', $entry->ip, '', '', 'This is bespoke to the member and used within the Moderation page to block an instructor. ') !!}
@endif

{!! HTMLFormHelper::radiogroup('Pre-Auth (Free Classes)', 'pre_authenticated', [1 => 'Yes', 0 => 'No'], $entry->pre_authenticated, $errors, [], 'The default setting here is ‘No’. If you wish to give a member free access to all classes, set the option to ‘Yes’.') !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors, [], 'A member who has completed the Registration Form and successfully verified their activation via the automated email received post registration, will be automatically listed as ‘Active’. If a member is shown to be ‘Inactive’ it is likely to be because they have not responded to an automated email, sent to the address specified in their Registration Form, requesting that they verify their activation. By ticking ‘Active’ the administrator will effectively verify activation on behalf of the member.') !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
