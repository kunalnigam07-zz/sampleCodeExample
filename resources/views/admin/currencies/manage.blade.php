@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\CurrencyController@edit', $entry->id) : action('Admin\CurrencyController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::text('Title *', 'title', $entry->title, $errors) !!}
{!! HTMLFormHelper::text('Currency Code *', 'code', $entry->code, $errors, ['maxlength' => 3], '3-letter ISO 4217 code.') !!}
{!! HTMLFormHelper::text('Symbol *', 'symbol', $entry->symbol, $errors) !!}
{!! HTMLFormHelper::text('Merchant ID *', 'merchant_id', $entry->merchant_id, $errors, [], 'Braintree Merchant Account ID for this currency.') !!}
{!! HTMLFormHelper::text('Rate *', 'rate', $entry->rate, $errors, [], 'Currency exchange of 1 GBP to this currency. This field is updated automatically, changes to it will not persist indefinitely.') !!}
{!! HTMLFormHelper::text('Min. Rate *', 'rate_min', $entry->rate_min, $errors, [], 'If rate above falls below the rate above, use this rate instead.') !!}
{!! HTMLFormHelper::text('Profit Margin *', 'profit_rate', $entry->profit_rate, $errors, [], 'Profit margin to add to the rate when currency conversion takes place.') !!}
{!! HTMLFormHelper::text('Ordering *', 'ordering', $entry->ordering, $errors) !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
