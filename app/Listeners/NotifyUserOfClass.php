<?php

namespace App\Listeners;

use EmailHelper;
use RouteHelper;
use MobileHelper;
use App\Events\ClassComingUp;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserOfClass
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ClassComingUp  $event
     * @return void
     */
    public function handle(ClassComingUp $event)
    {
        $user = $event->user;
        $class = $event->class;
        $template_id = $event->template_id;
        $comm_settings = $event->comm_settings;

        $link = '';
        if ($user->user_type == 2) { // Instructor
            $link = RouteHelper::getRoute('web.instructor.classes', [], true);
        } elseif ($user->user_type == 3) { // Member
            $link = RouteHelper::getRoute('web.member.classes', [], true);
        }

        if ($comm_settings['email'] == 1) {
            $params = [
                'NAME' => $user->name,
                'CLASS_NAME' => $class->title,
                'LINK' => $link
            ];
                      
            if ($user->user_type == 2) { // Temp
                EmailHelper::send($template_id, $user->email, $params);
            } // Temp              
        }

        if ($comm_settings['sms'] == 1) {
            if (strlen($user->mobile) > 0) {
                $params = [
                    'CLASS_NAME' => $class->title
                ];

                $number = '+' . $user->mobileCountry->dialing . $user->mobile;

                MobileHelper::sendFromTemplate($template_id, $number, $params);
            }
        }
    }
}
