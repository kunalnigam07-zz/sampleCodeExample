@extends('admin.layouts.login')
@section('body')
<div class="fake-table">
<div class="fake-table-cell">
<div id="login">
@include('admin.partials.alert')
<div class="top left clearfix">
<div class="logo left"><img src="{{ AssetHelper::asset('static/admin/images/id-logo.png') }}" alt="logo"></div>
<p>ADMIN<br><span>LOGIN PAGE</span></p>
</div>
<form class="clearfix" method="post" action="/admin/login">
<div class="fields">
<fieldset>
<input type="text" placeholder="EMAIL ADDRESS" name="email">
<span><i class="fa fa-user"></i></span>
</fieldset>
<fieldset>
<input type="password" placeholder="PASSWORD" name="password">
<span><i class="fa fa-key"></i></span>
</fieldset>
<input type="submit" value="OK">
</div>
<div class="bottom clearfix">
<input type="checkbox" name="rememberme" data-label="REMEMBER ME">
</div>
{!! csrf_field() !!}
</form>
</div>
</div>
</div>
@stop
