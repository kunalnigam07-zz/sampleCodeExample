<header class="clearfix">
<div class="user left clearfix">
<div class="avatar"><a href="/admin"><img src="{{ AssetHelper::asset('static/admin/images/avatar.png') }}" alt="user"></a></div>
<p>{{ Auth::user()->name . ' ' . Auth::user()->surname }}<br><span><i class="fa fa-clock-o"></i> {{ Auth::user()->timezone }}</span></p>
<a href="/admin/logout" class="logout"><i class="fa fa-power-off"></i></a>
</div>
<div class="search right clearfix">
<a href="/admin/logout" class="options"><i class="fa fa-power-off"></i></a>
<form onsubmit="return false;" id="global_search_form" style="display:none;">
<input type="text" id="global_search_box" placeholder="Search This Table...">     
</form>
</div>
</header>
