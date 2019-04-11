<div class="breadcrumbs clearfix">
<ul class="breadcrumbs left">
<li><a href="/admin">Dashboard</a></li>
@foreach ($page as $pdata)
	<li><i class="fa fa-angle-right"></i></li>
	@if (strlen($pdata[0]) > 0)
		<li><a href="/admin/{{ $pdata[0] }}">{{ $pdata[1] }}</a></li>
	@else
		<li>{{ $pdata[1] }}</li>
	@endif
@endforeach
</ul>
@if (isset($dt))
	@foreach ($dt['buttons'] as $v)
		<a href="{{ $v[2] }}" class="btn right" style="margin-left:5px;"><i class="fa fa-{{ $v[1] }}"></i>{{ $v[0] }}</a>
	@endforeach
@endif
<img src="{{ AssetHelper::asset('static/admin/images/spinner.gif') }}" alt="" class="table-loader" id="spinner" style="display:none;">
</div>
