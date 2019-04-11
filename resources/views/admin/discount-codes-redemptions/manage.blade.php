@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\DiscountCodeRedemptionController@edit', $entry->id) : action('Admin\DiscountCodeRedemptionController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::string('Member', $entry->member->name . ' ' . $entry->member->surname . ' (' . $entry->member->email . ')', '<a href="' . RouteHelper::userEditRoute($entry->member) . '">', '</a>') !!}
{!! HTMLFormHelper::string('Discount', $entry->discountCode->title, '<a href="' . action('Admin\DiscountCodeController@edit', $entry->discountCode->id) . '">', '</a>') !!}
{!! HTMLFormHelper::string('Date', DateHelper::showDate($entry->created_at)) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
