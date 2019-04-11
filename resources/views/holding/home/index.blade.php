@extends('holding.layouts.base')
@section('body')
<section class="content" id="prelaunch">
    <canvas></canvas>
    <div class="fake-table">
        <div class="fake-table-cell">
            <div class="nest">
                <img src="{{ AssetHelper::asset('static/holding/images/logo-big.png') }}" alt="img">
                @if (session()->has('flash_message_error'))
                    <h2 style="color:#EA445C;">Please complete all fields!</h2>
                @endif
                
                @if (session()->has('flash_message_info'))
                    <h2>Thank you!</h2>
                    <p>We will let you know the moment the site launches.</p>
                @else
                    <h2>Enter your details</h2>
                    <p>Tell us a bit about yourself and we'll notify you when we launch</p>
                    <form method="post" action="{{ route('web.holding.send') }}">
                        <input type="text" placeholder="Name" name="name" value="{{ old('name') }}">
                        <input type="email" placeholder="Email" name="email" value="{{ old('email') }}">
                        <label for="interests">What are your interests?</label>
                        <input type="text" placeholder="Yoga, HiiT, Bike, Pilates..." id="interests" name="interests" value="{{ old('interests') }}">
                        <div class="radio-btn clearfix">
                            <p class="left">Are you an instructor?</p>
                            <input type="radio" data-label="Yes" name="instructor" value="Yes" checked>
                            <input type="radio" data-label="No" name="instructor" value="No">
                        </div>
                        <input type="submit" value="Submit" class="btn orange">
                        {!! csrf_field() !!}
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>
@stop
