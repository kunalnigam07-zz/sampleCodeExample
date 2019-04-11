<?php

namespace App\Listeners;

use App\Events\ClassPublished;
use App\Helpers\EmailHelper;
use App\Models\Setting;
use Auth;

class SendEmailOnClassPublished
{
    const TEMPLATE_ID = 15;

    public function handle(ClassPublished $event)
    {
        if ($this->isBlockedEmail(Auth::user()->email)) {
            return;
        }

        $classEvents = $event->getClassEvents();
        if (!is_array($classEvents) || count($classEvents) < 1) {
            return;
        }
        $firstClassEvent = $classEvents[0];
        $emailTo = Setting::findOrFail(1);

        $params = [
            'TITLE'      => $firstClassEvent->title,
            'ABOUT'      => $firstClassEvent->about,
            'SIZE'       => $firstClassEvent->max_number,
            'INSTRUCTOR' => Auth::user()->name . ' ' . Auth::user()->surname,
            'DATE'       => $firstClassEvent->class_at->toDayDateTimeString(),
            'MULTI'      => count($classEvents) > 1 ? 'Yes' : 'No'
        ];

        EmailHelper::send(self::TEMPLATE_ID, $emailTo->class_published_notification_receiver, $params);
    }

    protected function isBlockedEmail($teacherEmail): bool
    {
        $arrayOfBlockedEmails = [
            'testrtc-instructor@fitswarm.com',
            'testrtc-instructor2@fitswarm.com',
            'testrtc-instructor3@fitswarm.com',
            'testrtc-instructor4@fitswarm.com',
            'testrtc-instructor5@fitswarm.com',
            'testRTC@testRTC.com'
        ];

        return in_array($teacherEmail, $arrayOfBlockedEmails);
    }
}
