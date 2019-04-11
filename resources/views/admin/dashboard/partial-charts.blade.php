<div class="clearfix">

<div class="charts col-md-5 no-space">
<div class="heading clearfix">
<h2 class="left"><i class="fa fa-area-chart"></i>MEMBERS REGISTERED - LAST 10 DAYS</h2>
</div>
<div class="chart">
<div class="ct-chart" id="chart1"></div>
</div>
</div>

<div class="charts col-md-5 no-space">
<div class="heading clearfix">
<h2 class="left"><i class="fa fa-area-chart"></i>INSTRUCTORS REGISTERED - LAST 10 DAYS</h2>
</div>
<div class="chart">
<div class="ct-chart" id="chart2"></div>
</div>
</div>

</div>

<div class="clearfix">

<div class="charts col-md-5 no-space">
<div class="heading clearfix">
<h2 class="left"><i class="fa fa-area-chart"></i>BOOKINGS MADE - LAST 10 DAYS</h2>
</div>
<div class="chart">
<div class="ct-chart" id="chart3"></div>
</div>
</div>

<div class="charts col-md-5 no-space">
<div class="heading clearfix">
<h2 class="left"><i class="fa fa-area-chart"></i>ORDERS PLACED - LAST 10 DAYS</h2>
</div>
<div class="chart">
<div class="ct-chart" id="chart4"></div>
</div>
</div>

</div>

{!! HTMLFormHelper::hidden('_token', csrf_token(), ['id' => 'token_zone']) !!}

@section('extra_footer')
<script src="{{ AssetHelper::asset('static/admin/js/dashboard.js') }}"></script>
@stop
