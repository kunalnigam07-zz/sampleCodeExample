@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\OrderController@edit', $entry->id) : action('Admin\OrderController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::legend('Order Details') !!}
{!! HTMLFormHelper::string('Order Number', $entry->order_number) !!}
{!! HTMLFormHelper::string('Total', '&pound;' . NumberHelper::money($entry->price)) !!}
{!! HTMLFormHelper::string('Member', $entry->member->name . ' ' . $entry->member->surname, '<a href="' . RouteHelper::userEditRoute($entry->member) . '">', '</a>') !!}
{!! HTMLFormHelper::string('Email', $entry->email, '<a href="mailto:' . $entry->email . '">', '</a>') !!}
{!! HTMLFormHelper::string('Date', DateHelper::showDate($entry->created_at)) !!}

<br>

{!! HTMLFormHelper::legend('Ordered Item') !!}
{!! HTMLFormHelper::string('£' . $entry->price . ($entry->foreign_price > 0 ? ' (' . $entry->foreign_price . ' ' . $entry->foreign_currency . ')' : ''), $entry->title) !!}


<br>

{!! HTMLFormHelper::legend('Billing Details') !!}
{!! HTMLFormHelper::string('Name', $entry->full_name) !!}
{!! HTMLFormHelper::string('Contact Number', $entry->mobile) !!}
{!! HTMLFormHelper::string('Country', $entry->country) !!}

<br>

{!! HTMLFormHelper::legend('Order Admin') !!}

{!! AuthHelper::isWL() ? '<div style="display:none;">' : '' !!}
{!! HTMLFormHelper::string('Gateway Response', $entry->gateway_response) !!}
{!! AuthHelper::isWL() ? '</div>' : '' !!}

{!! HTMLFormHelper::select('Status *', 'status_id', App\Models\OrderStatus::select(DB::raw('id, title'))->orderBy('title', 'ASC')->lists('title', 'id')->all(), $entry->status_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
