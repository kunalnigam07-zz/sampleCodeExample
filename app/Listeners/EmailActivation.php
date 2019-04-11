<?php

namespace App\Listeners;

use EmailHelper;
use RouteHelper;
use App\Events\MemberJoined;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailActivation
{
    /**
     * Handle the event.
     *
     * @param  MemberJoined  $event
     * @return void
     */
    public function handle(MemberJoined $event)
    {
        $user = $event->user;
        $settings = $event->settings;

        // Only send activation email if the setting is set as such via the CMS
        if (($user->user_type == 2 && $settings->reg_instructor_status == 1) || ($user->user_type == 3 && $settings->reg_member_status == 1)) {
            $token = str_random(50);

            $user->activation_token = $token;
            $user->save();

            $params = [
                'NAME' => $user->name,
                'LINK' => RouteHelper::getRoute('web.join.activate', [$token], true)
            ];
                    
            EmailHelper::send(1, $user->email, $params);
        }
    }
}
