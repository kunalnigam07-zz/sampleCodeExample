<?php

namespace App\Helpers;

use App\Models\Setting;
use App\Models\EmailTemplate;
use Twilio\Rest\Client as TwilioClient;

class MobileHelper
{
    public static function sendFromTemplate($template_id, $number, $params)
    {
        $template = EmailTemplate::findOrFail($template_id);
        $template = self::replaceParams($template, $params);

        self::send($number, $template->sms);
    }

    public static function send($number, $message)
    {
        $settings = Setting::findOrFail(1);
        $client = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));
        $is_plus_1 = false;
        if ($number[1] == 1) { // Country code starts with 1
            $is_plus_1 = true;
        }

        try {
            $sms = $client->messages->create(
                $number,
                [
                    'from' => ($is_plus_1 ? $settings->twilio_number_usa : $settings->twilio_number),
                    'body' => $message
                ]
            );

            return 'OK'; // Used by AJAX
        } catch (\Exception $e) {
            // Error, ignore 
        }

        return 'ERROR';
    }

    public static function replaceParams($template, $params)
    {
        foreach ($params as $k => $v) {
            $template->sms = str_replace('[#' . $k . '#]', $v, $template->sms);
        }

        return $template;
    }
}
