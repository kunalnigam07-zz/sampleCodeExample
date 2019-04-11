@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ action('Admin\LiveswitchSettingController@edit', 1) }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::legend('Instructor') !!}
{!! HTMLFormHelper::radiogroup('Enable Adaptive Upstream', 'ls_band_instr_adaptive_enabled', [1 => 'Yes', 0 => 'No'], $entry->ls_band_instr_adaptive_enabled, $errors, [], 'Whether to enable adaptive upstreams for instructors.') !!}
{!! HTMLFormHelper::text('Adaptive Upstream Percent', 'ls_band_instr_adaptive_perc', $entry->ls_band_instr_adaptive_perc, $errors, [], '% of upload bandwidth.', ['type' => 'pre', 'data' => '%']) !!}
{!! HTMLFormHelper::text('Adaptive Upstream Threshold', 'ls_band_instr_adaptive_thresh', $entry->ls_band_instr_adaptive_thresh, $errors, [], 'Threshold in kilobytes.', ['type' => 'pre', 'data' => 'KBps']) !!}

<br>

{!! HTMLFormHelper::legend('Student') !!}
{!! HTMLFormHelper::radiogroup('Enable Adaptive Upstream', 'ls_band_stud_adaptive_enabled', [1 => 'Yes', 0 => 'No'], $entry->ls_band_stud_adaptive_enabled, $errors, [], 'Whether to enable adaptive upstreams for students.') !!}
{!! HTMLFormHelper::text('Adaptive Upstream Percent', 'ls_band_stud_adaptive_perc', $entry->ls_band_stud_adaptive_perc, $errors, [], '% of upload bandwidth.', ['type' => 'pre', 'data' => '%']) !!}
{!! HTMLFormHelper::text('Adaptive Upstream Threshold', 'ls_band_stud_adaptive_thresh', $entry->ls_band_stud_adaptive_thresh, $errors, [], 'Threshold in kilobytes.', ['type' => 'pre', 'data' => 'KBps']) !!}
{!! HTMLFormHelper::text('Upstream RTT Threshold', 'ls_band_stud_up_rtt_thresh', $entry->ls_band_stud_up_rtt_thresh, $errors, [], 'Threshold in kilobytes.', ['type' => 'pre', 'data' => 'KBps']) !!}
<!--
{!! HTMLFormHelper::text('Upstream RTT Presets', 'ls_band_stud_up_presets', $entry->ls_band_stud_up_presets, $errors, [], 'Threshold in kilobytes.', ['type' => 'pre', 'data' => 'KBps']) !!}
-->

{!! HTMLFormHelper::legend('AWS') !!}

<div class="field">
    @if ($instanceIsRunning)
        <a
            href="{{ action('Admin\LiveswitchSettingController@stopAwsInstance') }}"
            class="btn"
            style="background-color: #9d0505;padding: 15px"
        >
            Stop instance
        </a>
    @else
        <a
            href="{{ action('Admin\LiveswitchSettingController@startAwsInstance') }}"
            class="btn"
            style="background-color: #00FF00;padding: 15px"
        >
            Start instance
        </a>
    @endif
</div>

{!! HTMLFormHelper::submit('Save Changes', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
