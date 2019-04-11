<?php

namespace App\Listeners;

use DateHelper;
use RouteHelper;
use EmailHelper;
use ClassHelper;
use App\Models\User;
use App\Models\CommunicationSetting;
use App\Events\InstructorInvitedMember;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailInstructorInvite
{
    /**
     * Handle the event.
     *
     * @param  InstructorInvitedMember  $event
     * @return void
     */
    public function handle(InstructorInvitedMember $event)
    {
        $send_email = true;
        $classinvite = $event->classinvite;
        $instructor = $event->instructor;
        $timezone = 'Europe/London';
        
        // check if this email has account AND account allows emails...
        $member_rec = User::members()->where('status', 1)->where('email', $classinvite->email)->first();
        if (count($member_rec) == 1) {
            $comm_setting = CommunicationSetting::where('user_id', $member_rec->id)->where('type_id', 4)->where('email', 0)->count();
            if ($comm_setting == 1) {
                $send_email = false;
            }
            $timezone = $member_rec->timezone; // If email belongs to member, use their timezone
        }

        if ($send_email) {
            $class = $classinvite->classEvent;

            $params = [
                'NAME' => $classinvite->name,
                'INSTRUCTOR' => $instructor->name . ' ' . $instructor->surname,
                'CLASS_NAME' => $class->title,
                'CLASS_DATE' => DateHelper::showDateTZ($class->class_at, 'l, j F', $timezone),
                'CLASS_TIME' => DateHelper::showDateTZ($class->class_at, 'H:i', $timezone) . ' <b>' . DateHelper::friendlyTimezone($timezone) . '</b>',
                'CLASS_PRICE' => ClassHelper::cost($class->price),
                'LINK' => RouteHelper::getRoute('web.invite.fromInstructor', $classinvite->guid, true)
            ];

            EmailHelper::send(4, $classinvite->email, $params);
        }
    }
}
