<div class="tables clearfix">
<table class="datatable adm-table">
<thead>
<tr>
@foreach ($dt['labels'] as $v)
    <th>{{ strtoupper($v) }}{!! ($v != 'Actions') ? '<span class="order"></span>' : '' !!}</th>
@endforeach
</tr>
</thead>
</table>
</div>
