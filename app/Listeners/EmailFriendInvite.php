<?php

namespace App\Listeners;

use AuthHelper;
use DateHelper;
use ClassHelper;
use EmailHelper;
use RouteHelper;
use StringHelper;
use App\Events\FriendInvited;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailFriendInvite
{
    /**
     * Handle the event.
     *
     * @param  FriendInvited  $event
     * @return void
     */
    public function handle(FriendInvited $event)
    {
        $data = $event->data;
        $class = $data['class'];
        $timezone = AuthHelper::isLoggedInMemberOrInstructor() ? AuthHelper::user()->timezone : 'Europe/London'; // If logged in user, use their timezone

        $params = [
            'NAME' => $data['friend_name'],
            'FRIEND_NAME' => $data['sender_name'],
            'CLASS_NAME' => $class->title,
            'CLASS_DATE' => DateHelper::showDateTZ($class->class_at, 'l, j F', $timezone),
            'CLASS_TIME' => DateHelper::showDateTZ($class->class_at, 'H:i', $timezone) . ' <b>' . DateHelper::friendlyTimezone($timezone) . '</b>',
            'CLASS_PRICE' => ClassHelper::cost($class->price),
            'LINK' => RouteHelper::getRoute('web.class.details', [$class->id, StringHelper::slug($class->title)], true)
        ];
                    
        EmailHelper::send(3, $data['friend_email'], $params);
    }
}
