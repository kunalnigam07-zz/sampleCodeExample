@if (Session::has('flash_message_error'))
<div class="alerts"><div class="error"><p><strong>ERROR!</strong> 
{{ Session::get('flash_message_error') }}
</div></div>
@endif

@if (Session::has('flash_message_success'))
<div class="alerts"><div class="success"><p><strong>SUCCESS!</strong> 
{{ Session::get('flash_message_success') }}
</div></div>
@endif

@if (Session::has('flash_message_info'))
<div class="alerts"><div class="info"><p><strong>INFO!</strong> 
{{ Session::get('flash_message_info') }}
</div></div>
@endif
