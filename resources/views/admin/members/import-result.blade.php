@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ action('Admin\MemberController@importFinalise') }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::legend('Import Confirmation') !!}
{!! $results['message'] !!}

@if (count($results['members']) > 0)
    <table id="attendance-table"><thead><tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Password</th><th>Mobile</th><th>DOB</th></tr></thead><tbody>
    @foreach ($results['members'] as $k => $v)
        <tr>
            <td>{{ $v[0] }}</td>
            <td>{{ $v[1] }}</td>
            <td>{{ $v[2] }}</td>
            <td>{{ $v[3] }}</td>
            <td>{{ $v[4] }}</td>
            <td>{{ Carbon::parse(DateHelper::excel($v[5], '', $results['dateformat']))->format('d F Y') }}</td>
        </tr>
    @endforeach
    </tbody></table><br>
@endif

@if ($results['error'] == false)    
    {!! HTMLFormHelper::hidden('import_id', $results['id']) !!}
    {!! HTMLFormHelper::submit('Confirm Import', ['class' => 'right']) !!}
@endif

</div>
{!! csrf_field() !!}
</form>
@stop
