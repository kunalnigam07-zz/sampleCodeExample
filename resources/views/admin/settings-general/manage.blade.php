@extends('admin.layouts.base')
@section('body')
<form id="file-upload" class="upload" method="post" action="{{ action('Admin\SettingController@edit', 1) }}" enctype="multipart/form-data">
<div class="inner clearfix">
{!! HTMLFormHelper::legend('Landing Page') !!}
{!! HTMLFormHelper::string('Landing Page Key', config('services.landingpage.key')) !!}

<br>

{!! HTMLFormHelper::legend('Theme Settings') !!}
{!! HTMLFormHelper::text('Website Name *', 'site_name', $entry->site_name, $errors, [], 'Used in the footer, some meta tags, etc.') !!}
{!! HTMLFormHelper::file('Logo', 'logo', $entry->logo, $errors, [], 'Used on the website and emails.', ['type' => 'image', 'preview' => AssetHelper::file('theme', $entry->id, $entry->logo, '200x0')]) !!}
{!! HTMLFormHelper::file('Live Logo', 'logo_live', $entry->logo_live, $errors, [], 'Smaller logo used in the footer of live classes.', ['type' => 'image', 'preview' => AssetHelper::file('theme', $entry->id, $entry->logo_live, '0x24')]) !!}
{!! HTMLFormHelper::file('Open Graph Logo', 'logo_og', $entry->logo_og, $errors, [], 'Used for sites (like Facebook and Twitter) that use Open Graph tags.', ['type' => 'image', 'preview' => AssetHelper::file('theme', $entry->id, $entry->logo_og, '200x0')]) !!}
{!! HTMLFormHelper::file('Mobile Logo', 'logo_mobile', $entry->logo_mobile, $errors, [], 'Smaller logo used in the footer of live classes for mobile.', ['type' => 'image', 'preview' => AssetHelper::file('theme', $entry->id, $entry->logo_mobile, '0x41')]) !!}
{!! HTMLFormHelper::file('CSS File', 'css_file', $entry->css_file, $errors, [], '', ['type' => 'file', 'preview' => AssetHelper::file('theme', $entry->id, $entry->css_file, '')]) !!}

<br>

{!! HTMLFormHelper::legend('Twilio Settings') !!}
{!! HTMLFormHelper::text('Twilio Number *', 'twilio_number', $entry->twilio_number, $errors, [], 'The "from" number to use, available in your Twilio console.') !!}
{!! HTMLFormHelper::text('Twilio USA Number *', 'twilio_number_usa', $entry->twilio_number_usa, $errors, [], 'The "from" number to use for users based in the USA.') !!}

<br>

{!! HTMLFormHelper::legend('TokBox Settings') !!}
{!! HTMLFormHelper::text('TokBox API Key *', 'tokbox_key', $entry->tokbox_key, $errors) !!}
{!! HTMLFormHelper::text('TokBox API Secret *', 'tokbox_secret', $entry->tokbox_secret, $errors) !!}
{!! HTMLFormHelper::text('TokBox API Key (Dev)', 'tokbox_key_dev', $entry->tokbox_key_dev, $errors, [], 'Used for testing classes utilising different TokBox API credentials.') !!}
{!! HTMLFormHelper::text('TokBox API Secret (Dev)', 'tokbox_secret_dev', $entry->tokbox_secret_dev, $errors, [], 'Used for testing classes utilising different TokBox API credentials.') !!}

<br>

{!! HTMLFormHelper::legend('LiveSwitch Settings') !!}
{!! HTMLFormHelper::text('LiveSwitch Gateway URL *', 'liveswitch_url', $entry->liveswitch_url, $errors) !!}
{!! HTMLFormHelper::text('LiveSwitch App Name *', 'liveswitch_key', $entry->liveswitch_key, $errors) !!}
{!! HTMLFormHelper::text('LiveSwitch App Secret *', 'liveswitch_secret', $entry->liveswitch_secret, $errors) !!}
<br>

{!! HTMLFormHelper::legend('Test LiveSwitch Settings') !!}
{!! HTMLFormHelper::text('LiveSwitch Gateway URL *', 'test_liveswitch_url', $entry->test_liveswitch_url, $errors) !!}
{!! HTMLFormHelper::text('LiveSwitch App Name *', 'test_liveswitch_key', $entry->test_liveswitch_key, $errors) !!}
{!! HTMLFormHelper::text('LiveSwitch App Secret *', 'test_liveswitch_secret', $entry->test_liveswitch_secret, $errors) !!}
<br>

{!! HTMLFormHelper::legend('AWS Settings') !!}
{!! HTMLFormHelper::text('Instance ID', 'aws_instance_id', $entry->aws_instance_id, $errors) !!}
{!! HTMLFormHelper::text('Access key id', 'aws_access_key_id', $entry->aws_access_key_id, $errors) !!}
{!! HTMLFormHelper::text('Secret access key', 'aws_secret_access_key', $entry->aws_secret_access_key, $errors) !!}
<br>

{!! HTMLFormHelper::legend('LiveSwitch Xirsys Settings') !!}
{!! HTMLFormHelper::text('Xirsys Ident*', 'liveswitch_xirsys_ident', $entry->liveswitch_xirsys_ident, $errors) !!}
{!! HTMLFormHelper::text('Xirsys Secret *', 'liveswitch_xirsys_secret', $entry->liveswitch_xirsys_secret, $errors) !!}
{!! HTMLFormHelper::text('Xirsys Channel *', 'liveswitch_xirsys_channel', $entry->liveswitch_xirsys_channel, $errors) !!}
<br>

{!! HTMLFormHelper::legend('COTURN Settings') !!}
{!! HTMLFormHelper::text('Coturn Host (IP:PORT)*', 'coturn_host', $entry->coturn_host, $errors) !!}
{!! HTMLFormHelper::text('Coturn Secret *', 'coturn_secret', $entry->coturn_secret, $errors) !!}
{!! HTMLFormHelper::text('Coturn Name', 'coturn_name', $entry->coturn_name, $errors) !!}
<br>

{!! HTMLFormHelper::legend('Email Recipients') !!}
{!! HTMLFormHelper::text('Feedback Form *', 'email_feedback', $entry->email_feedback, $errors, [], 'Comma-separate email addresses.') !!}
{!! HTMLFormHelper::text('Report Email *', 'email_report', $entry->email_report, $errors, [], 'Comma-separate email addresses. Email to which reported members/classes are sent.') !!}

<br>

{!! HTMLFormHelper::legend('Notification Emails Sent Out') !!}
{!! HTMLFormHelper::text('From Name *', 'email_notifications_name', $entry->email_notifications_name, $errors) !!}
{!! HTMLFormHelper::text('From Email *', 'email_notifications', $entry->email_notifications, $errors) !!}

<br>

{!! HTMLFormHelper::legend('Social Configuration') !!}
{!! HTMLFormHelper::text('Facebook', 'social_facebook', $entry->social_facebook, $errors) !!}
{!! HTMLFormHelper::text('Twitter', 'social_twitter', $entry->social_twitter, $errors) !!}
{!! HTMLFormHelper::text('Instagram', 'social_instagram', $entry->social_instagram, $errors) !!}
{!! HTMLFormHelper::text('YouTube', 'social_youtube', $entry->social_youtube, $errors) !!}

<br>

{!! HTMLFormHelper::legend('Global Code Insertion') !!}
{!! HTMLFormHelper::textarea('Inside &lt;head&gt;', 'code_head', $entry->code_head, $errors) !!}
{!! HTMLFormHelper::textarea('After &lt;body&gt;', 'code_body', $entry->code_body, $errors) !!}
{!! HTMLFormHelper::textarea('Inside Footer', 'code_footer', $entry->code_footer, $errors) !!}

<br>

{!! HTMLFormHelper::legend('Join Confirmation Page Tracking') !!}
{!! HTMLFormHelper::textarea('Instructor Joined', 'join_code_instructor', $entry->join_code_instructor, $errors, [], 'Code placed in the body and run on the confirmation page after an instructor joins.') !!}
{!! HTMLFormHelper::textarea('Member Joined', 'join_code_member', $entry->join_code_member, $errors, [], 'Code placed in the body and run on the confirmation page after a member joins.') !!}

<br>

{!! HTMLFormHelper::legend('System Check Settings - Speed of Me') !!}
{!! HTMLFormHelper::text('SoM API Key *', 'som_api', $entry->som_api, $errors) !!}
{!! HTMLFormHelper::text('SoM Domain *', 'som_domain', $entry->som_domain, $errors) !!}
{!! HTMLFormHelper::text('Member Sustain Time *', 'som_m_sustain', $entry->som_m_sustain, $errors, [], 'Number 1 - 8.') !!}
{!! HTMLFormHelper::text('Instructor Sustain Time *', 'som_i_sustain', $entry->som_i_sustain, $errors, [], 'Number 1 - 8.') !!}
{!! HTMLFormHelper::text('SoM Factor (%) *', 'som_bandwidth_factor', $entry->som_bandwidth_factor, $errors, [], 'How much of the reported Speed of Me Bandwidth to use % (25-100)') !!}

<br>

{!! HTMLFormHelper::legend('System Check Settings - Downloads') !!}
{!! HTMLFormHelper::text('Member Red Kbps *', 'som_m_red', $entry->som_m_red, $errors, [], 'Download speeds below this will fail (red).', ['type' => 'pre', 'data' => 'Kbps']) !!}
{!! HTMLFormHelper::text('Member Green Kbps *', 'som_m_green', $entry->som_m_green, $errors, [], 'Download speeds above this will pass (green). Speeds between red and green will pass (amber).', ['type' => 'pre', 'data' => 'Kbps']) !!}
{!! HTMLFormHelper::text('Instructor Red Kbps *', 'som_i_red', $entry->som_i_red, $errors, [], 'Download speeds below this will fail (red).', ['type' => 'pre', 'data' => 'Kbps']) !!}
{!! HTMLFormHelper::text('Instructor Green Kbps *', 'som_i_green', $entry->som_i_green, $errors, [], 'Download speeds above this will pass (green). Speeds between red and green will pass (amber).', ['type' => 'pre', 'data' => 'Kbps']) !!}

<br>

{!! HTMLFormHelper::legend('System Check Settings - Uploads') !!}
{!! HTMLFormHelper::text('Member Red Kbps *', 'som_m_upload_red', $entry->som_m_upload_red, $errors, [], 'Upload speeds below this will fail (red).', ['type' => 'pre', 'data' => 'Kbps']) !!}
{!! HTMLFormHelper::text('Member Green Kbps *', 'som_m_upload_green', $entry->som_m_upload_green, $errors, [], 'Upload speeds above this will pass (green). Speeds between red and green will pass (amber).', ['type' => 'pre', 'data' => 'Kbps']) !!}
{!! HTMLFormHelper::text('Instructor Red Kbps *', 'som_i_upload_red', $entry->som_i_upload_red, $errors, [], 'Upload speeds below this will fail (red).', ['type' => 'pre', 'data' => 'Kbps']) !!}
{!! HTMLFormHelper::text('Instructor Green Kbps *', 'som_i_upload_green', $entry->som_i_upload_green, $errors, [], 'Upload speeds above this will pass (green). Speeds between red and green will pass (amber).', ['type' => 'pre', 'data' => 'Kbps']) !!}

<div style="display:none;">
{!! HTMLFormHelper::text('Member Kbps *', 'sc_member_kbps', $entry->sc_member_kbps, $errors, [], '', ['type' => 'pre', 'data' => 'Kbps']) !!}
{!! HTMLFormHelper::text('Member Duration *', 'sc_member_duration', $entry->sc_member_duration, $errors, [], '', ['type' => 'pre', 'data' => 'Seconds']) !!}
{!! HTMLFormHelper::text('Instructor Kbps *', 'sc_instructor_kbps', $entry->sc_instructor_kbps, $errors, [], '', ['type' => 'pre', 'data' => 'Kbps']) !!}
{!! HTMLFormHelper::text('Instructor Duration *', 'sc_instructor_duration', $entry->sc_instructor_duration, $errors, [], '', ['type' => 'pre', 'data' => 'Seconds']) !!}
</div>

<br>

{!! HTMLFormHelper::legend('Publish Resolutions & FPS') !!}
{!! HTMLFormHelper::text('Instructor Res. *', 'ins_res', $entry->ins_res, $errors, [], 'Valid values: 320x240, 640x480 or 1280x720.') !!}
{!! HTMLFormHelper::text('Instructor FPS *', 'ins_fps', $entry->ins_fps, $errors, [], 'Valid values: 1, 7, 15 or 30.') !!}
{!! HTMLFormHelper::text('Member Res. #1 *', 'mem_res_1', $entry->mem_res_1, $errors, [], 'First member range. Valid values: 320x240, 640x480 or 1280x720.') !!}
{!! HTMLFormHelper::text('Member Res. #2 *', 'mem_res_2', $entry->mem_res_2, $errors, [], 'Second member range. Valid values: 320x240, 640x480 or 1280x720.') !!}
{!! HTMLFormHelper::text('Member Boundary *', 'mem_res_boundary', $entry->mem_res_boundary, $errors, [], 'The boundary between Member Res. #1 and #2. If number of bookings is <= this value, use Member Res. #1, else use Member Res. #2.') !!}
{!! HTMLFormHelper::text('Member FPS *', 'mem_fps', $entry->mem_fps, $errors, [], 'Valid values: 1, 7, 15 or 30.') !!}

<br>

{!! HTMLFormHelper::legend('Class published notification') !!}
{!! HTMLFormHelper::text('Receiver', 'class_published_notification_receiver', $entry->class_published_notification_receiver, $errors, [], '') !!}

{!! HTMLFormHelper::submit('Save Changes', ['class' => 'right']) !!}
</div>
{!! csrf_field() !!}
</form>
@stop
