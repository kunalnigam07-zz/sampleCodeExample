@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ isset($entry->id) ? action('Admin\ClassEventController@edit', $entry->id) : action('Admin\ClassEventController@create') }}" enctype="multipart/form-data">
<div class="inner clearfix">

@if ($entry->actual_start_at != null || $entry->actual_end_at != null || $entry->cancelled_at != null || strlen($entry->tb_session) > 0)
    {!! HTMLFormHelper::legend('Information') !!}
    {!! $entry->actual_start_at != null ? HTMLFormHelper::string('Actual Start', DateHelper::showDate($entry->actual_start_at)) : '' !!}
    {!! $entry->actual_end_at != null ? HTMLFormHelper::string('Actual End', DateHelper::showDate($entry->actual_end_at)) : '' !!}
    {!! $entry->cancelled_at != null ? HTMLFormHelper::string('Cancelled On', DateHelper::showDate($entry->cancelled_at)) : '' !!}
    {!! strlen($entry->tb_session) > 0 ? HTMLFormHelper::string('TokBox Session', $entry->tb_session) : '' !!}
    <br>
@endif

{!! HTMLFormHelper::legend('Class Details') !!}

<p class="dox-detail-heading">These sectors are straight forward and simply provide details of the class offered by an instructor. All of this information can be edited by simply replacing the current information in the relevant boxes.</p>

{!! HTMLFormHelper::text('Class Name *', 'title', $entry->title, $errors) !!}
{!! HTMLFormHelper::select('Type *', 'type_array', $types_array, ($entry->type_1_id . '_' . $entry->type_2_id . '_' . $entry->type_3_id), $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::select('Instructor *', 'user_id', $instructors_array, $entry->user_id, $errors, ['class' => 'chosen-select']) !!}
{!! HTMLFormHelper::text('Price *', 'price', $entry->price, $errors, [], '', ['type' => 'pre', 'data' => '£']) !!}
{!! HTMLFormHelper::text('Class Date & Time *', 'class_at', DateHelper::showDate($entry->class_at), $errors, ['data-date-format' => 'YYYY-MM-DD H:mm:ss'], '', ['type' => 'date', 'data' => '']) !!}

@if (isset($entry->id))
    {!! HTMLFormHelper::text('Class End Time *', 'class_ends_at', DateHelper::showDate($entry->class_ends_at), $errors, ['data-date-format' => 'YYYY-MM-DD H:mm:ss'], '', ['type' => 'date', 'data' => '']) !!}
@else
    {!! HTMLFormHelper::text('Duration *', 'dur_', '', $errors, [], 'Class duration in minutes.') !!}
@endif

{!! HTMLFormHelper::textarea('About', 'about', $entry->about, $errors) !!}

<br>

{!! HTMLFormHelper::legend('Restrictions & Limits') !!}
{!! HTMLFormHelper::text('Maximum Members *', 'max_number', $entry->max_number, $errors, [], 'The total number of participants the instructor has specified can book for the class i.e. the maximum class size.') !!}
{!! HTMLFormHelper::radiogroup('Level', 'level', [1 => ClassHelper::level(1), 2 => ClassHelper::level(2), 3 => ClassHelper::level(3)], $entry->level, $errors, [], 'Indication of the skill/ability level the class is geared towards.') !!}
{!! HTMLFormHelper::radiogroup('Privacy', 'privacy', [1 => 'Public', 0 => 'Private'], $entry->privacy, $errors, [], 'This relates to the class accessibility. Public classes can be viewed and booked by all members. Private can only be viewed and booked by members invited by the Instructor.') !!}
{!! HTMLFormHelper::radiogroup('Bulk Allowed', 'bulk_allowed', [1 => 'Yes', 0 => 'No'], $entry->bulk_allowed, $errors, [], 'Relates to whether or not the instructor allows the class to be booked as part of a bulk payment option.') !!}

{!! AuthHelper::isWL() ? '<div style="display:none;">' : '' !!}
{!! HTMLFormHelper::radiogroup('Has Music Option', 'has_music', [1 => 'Yes', 0 => 'No'], $entry->has_music, $errors) !!}
{!! HTMLFormHelper::radiogroup('Is Broadcast Only', 'broadcast', [1 => 'Yes', 0 => 'No'], $entry->broadcast, $errors) !!}
{!! AuthHelper::isWL() ? '</div>' : '' !!}

{!! HTMLFormHelper::radiogroup('Record Class?', 'record_class', [1 => 'Yes', 0 => 'No'], $entry->record_class, $errors, [], 'Whether this class should be recorded (archived). This setting cannot be changed after a class has been started.') !!}

<br>

{!! HTMLFormHelper::legend('Equipment Required') !!}

<div id="golden_copy_equipment">
<div style="border:1px dotted #E5E5E5; padding: 10px; margin-bottom:15px; display: none;">
{!! HTMLFormHelper::text('Equipment *', 'equipment_title[]', '', $errors) !!}
{!! HTMLFormHelper::text('Ordering *', 'equipment_ordering[]', '', $errors) !!}
<a href="#" class="btn right" onclick="$(this).parent('div').remove();return false;"><i class="fa fa-trash"></i>Remove</a>
<div class="clearfix"></div>
</div>
</div>

@foreach ($entry->equipment as $eqs)
    <div style="border:1px dotted #E5E5E5; padding: 10px; margin-bottom:15px;">
    {!! HTMLFormHelper::text('Equipment *', 'equipment_title[]', $eqs->title, $errors) !!}
    {!! HTMLFormHelper::text('Ordering *', 'equipment_ordering[]', $eqs->ordering, $errors) !!}
    <a href="#" class="btn right" onclick="$(this).parent('div').remove();return false;"><i class="fa fa-trash"></i>Remove</a>
    <div class="clearfix"></div>
    </div>
@endforeach

<a href="#" class="btn left" onclick="addEquipment();return false;" id="add-equipment-button"><i class="fa fa-plus"></i>Add Equipment</a>
<div class="clearfix" style="margin-bottom:15px;"></div>

<br>

{!! HTMLFormHelper::legend('Additional Information') !!}

<p class="dox-detail-heading">Enables the Administrator to add or remove a URL for a video to be displayed on the class page the Members will view, plus edit an image or apply a simple colour background.</p>

{!! HTMLFormHelper::text('Video URL', 'video_url', $entry->video_url, $errors) !!}
{!! HTMLFormHelper::file('Image', 'img', $entry->img, $errors, [], '', ['type' => 'image', 'preview' => AssetHelper::file('classes', $entry->id, $entry->img, '200x0')]) !!}
{!! HTMLFormHelper::text('Flat Colour', 'flat_colour', $entry->flat_colour, $errors, ['maxlength' => 6], 'Hex code for the flat colour.', ['type' => 'pre', 'data' => '#']) !!}

{!! AuthHelper::isWL() ? '<div style="display:none;">' : '' !!}
<br>
{{--
{!! HTMLFormHelper::legend('Optional TokBox Override Settings') !!}
{!! HTMLFormHelper::radiogroup('TokBox API Choice', 'tokbox_api_choice', [1 => 'Default', 2 => 'Dev'], $entry->tokbox_api_choice, $errors, [], 'Whether this class should use the default TokBox API credentials or the specified dev credentials.') !!}
{!! HTMLFormHelper::text('Instructor TokBox JS URL', 'tokbox_js_instructor', $entry->tokbox_js_instructor, $errors, [], 'Ignore this field to use the latest stable version.') !!}
{!! HTMLFormHelper::text('Member TokBox JS URL', 'tokbox_js_member', $entry->tokbox_js_member, $errors, [], 'Ignore this field to use the latest stable version.') !!}
{!! AuthHelper::isWL() ? '</div>' : '' !!}

<br>
--}}

{!! HTMLFormHelper::legend('Optional LiveSwitch Settings') !!}
{!! HTMLFormHelper::text('Application ID', 'liveswitch_appid', $entry->liveswitch_appid, $errors, [], 'Leave this field empty, to use the default Application ID.') !!}
{!! AuthHelper::isWL() ? '</div>' : '' !!}

<br>

{!! HTMLFormHelper::legend('Administrative') !!}
{!! HTMLFormHelper::text('Parent *', 'parent_id', $entry->parent_id, $errors, [], 'Relates to whether or not the class is a part of a series. If it is, the parent ID will be displayed here.') !!}
{!! HTMLFormHelper::radiogroup('Published', 'published', [1 => 'Yes', 0 => 'No'], $entry->published, $errors, [], 'Relates to whether or not the class is viewable and accessible for members to book.') !!}
{!! HTMLFormHelper::radiogroup('Status', 'status', [1 => 'Active', 0 => 'Inactive'], $entry->status, $errors) !!}
{!! HTMLFormHelper::submit(isset($entry->id) ? 'Save Changes' : 'Create New', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
@section('extra_footer')
<script>
function addEquipment() {
    var html = $('#golden_copy_equipment').html();
    html = html.replace('display: none;', '');
    $('#add-equipment-button').before(html);
}
</script>
@stop
