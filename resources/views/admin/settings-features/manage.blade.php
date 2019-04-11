@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ action('Admin\FeatureSettingController@edit', 1) }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::legend('Registration') !!}
{!! HTMLFormHelper::radiogroup('Allow Registration', 'reg_allowed', [1 => 'Yes', 0 => 'No'], $entry->reg_allowed, $errors, [], 'Whether new registrations are allowed on the website.') !!}
{!! HTMLFormHelper::radiogroup('Member Reg. Status', 'reg_member_status', [1 => 'Activation Email', 0 => 'Admin Activated'], $entry->reg_member_status, $errors, [], 'Whether the member signing up can activate their account via the automatic activation email, or whether manual activation by admin is required.') !!}
{!! HTMLFormHelper::radiogroup('Instructor Reg. Status', 'reg_instructor_status', [1 => 'Activation Email', 0 => 'Admin Activated'], $entry->reg_instructor_status, $errors, [], 'Whether the instructor signing up can activate their account via the automatic activation email, or whether manual activation by admin is required.') !!}

<br>

{!! HTMLFormHelper::legend('Financial') !!}
{!! HTMLFormHelper::text('Margin *', 'margin_percentage', $entry->margin_percentage, $errors, [], '% margin kept by the system, rest goes to instructor.', ['type' => 'pre', 'data' => '%']) !!}
{!! HTMLFormHelper::text('Min. Class Price *', 'min_class_price', $entry->min_class_price, $errors, [], 'Minimum class price an instructor may set.', ['type' => 'pre', 'data' => '£']) !!}
{!! HTMLFormHelper::text('Min. ' . ClassHelper::bulkType(0) . ' Price *', 'min_1on1_price', $entry->min_1on1_price, $errors, [], 'Minimum ' . ClassHelper::bulkType(0) . ' price for bulk payments an instructor may set.', ['type' => 'pre', 'data' => '£']) !!}
{!! HTMLFormHelper::text('Min. ' . ClassHelper::bulkType(1) . ' Price *', 'min_group_price', $entry->min_group_price, $errors, [], 'Minimum ' . ClassHelper::bulkType(1) . ' price for bulk payments an instructor may set.', ['type' => 'pre', 'data' => '£']) !!}

<br>

{!! HTMLFormHelper::legend('Classes') !!}
{!! HTMLFormHelper::radiogroup('Free Classes', 'free_option', [1 => 'User Defined', 2 => 'All Classes Free To All Users'], $entry->free_option, $errors, [], 'If set as "User Defined", only users marked "Pre-Authenticated" will have free access to all classes.') !!}
{!! HTMLFormHelper::text('Max. Title Length', 'max_class_title_length', $entry->max_class_title_length, $errors, [], 'Maximum length of title allowed.') !!}
{!! HTMLFormHelper::text('Max. Class Members *', 'max_class_participants_limit', $entry->max_class_participants_limit, $errors, [], 'Maximum number of members a class may support.') !!}
{!! HTMLFormHelper::text('Max. Recurring Classes *', 'max_recurring_classes_limit', $entry->max_recurring_classes_limit, $errors, [], 'Maximum number of recurring classes that can be set when adding a class.') !!}
{!! HTMLFormHelper::text('Cancellation Cut-off *', 'class_cancellation_mins', $entry->class_cancellation_mins, $errors, [], 'Number of minutes after which a class is cancelled automatically if the instructor didn\'t start it.') !!}
{!! HTMLFormHelper::text('Booking Lateness *', 'booking_mins_after_class_start', $entry->booking_mins_after_class_start, $errors, [], 'Number of minutes after class start wherein bookings can still be made.') !!}

{!! HTMLFormHelper::legend('Classes Rotation') !!}
{!! HTMLFormHelper::radiogroup('Enable Rotation', 'rotation_enabled', [1 => 'Enable', 0 => 'Disable'], $entry->rotation_enabled, $errors, [], 'Enable/Disable students rotation feature.') !!}
{!! HTMLFormHelper::text('Students on Screen*', 'rotation_default_on_screen', $entry->rotation_default_on_screen, $errors, [], 'Default number of students on the screen.') !!}
{!! HTMLFormHelper::text('Max. Students on Screen *', 'rotation_max_on_screen', $entry->rotation_max_on_screen, $errors, [], 'Maximum number of students on the screen.') !!}
{!! HTMLFormHelper::text('Rotate Count *', 'rotation_default_rotate', $entry->rotation_default_rotate, $errors, [], 'Default number of students to rotate each time.') !!}
{!! HTMLFormHelper::text('Max. Rotate *', 'rotation_max_rotate', $entry->rotation_max_rotate, $errors, [], 'Maximum number of students to rotate each time.') !!}
{!! HTMLFormHelper::text('Rotation Frequency *', 'rotation_default_seconds', $entry->rotation_default_seconds, $errors, [], 'Default rotation frequency (seconds).') !!}
{!! HTMLFormHelper::text('Max. Rotation Frequency *', 'rotation_max_seconds', $entry->rotation_max_seconds, $errors, [], 'Maximum rotation frequency (seconds).') !!}
{!! HTMLFormHelper::text('Min. Rotation Frequency *', 'rotation_min_seconds', $entry->rotation_min_seconds, $errors, [], 'Minimum rotation frequency (seconds).') !!}

{!! HTMLFormHelper::submit('Save Changes', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
