<?php

namespace App\Helpers;

use Mail;
use App\Models\EmailTemplate;

class EmailHelper
{
	public static function send($template, $email, $params)
	{
		$template = EmailTemplate::findOrFail($template);
		$template = self::replaceParams($template, $params);
		$data = [
			'email' => $email,
			'from_email' => $template->from_email,
			'from_name' => $template->from_name,
			'subject' => $template->subject,
			'html' => $template->html_version,
			'text' => $template->text_version
		];
		
		Mail::send(['emails.web.template-html', 'emails.web.template-text'], $data, function ($message) use ($data) {
			$message->to($data['email'])
				->from($data['from_email'], $data['from_name'])
				->subject($data['subject'])
                ->getSwiftMessage()
                ->getHeaders()
                ->addTextHeader('x-mailgun-native-send', 'true');
		});
	}

	public static function replaceParams($template, $params)
	{
		foreach ($params as $k => $v) {
			$template->html_version = str_replace('[#' . $k . '#]', nl2br($v), $template->html_version);
			$template->text_version = str_replace('[#' . $k . '#]', strip_tags($v), $template->text_version);

			$template->subject = str_replace('[#' . $k . '#]', $v, $template->subject);
		}
		
		// Because TinyMCE adds a path in front of the token, remove this path here
		$template->html_version = str_replace(config('app.url') . '/admin/settings/emails/edit/', '', $template->html_version);
	    //$template->html_version = str_replace('https://' . $_SERVER['HTTP_HOST'] . '/admin/settings/emails/edit/', '', $template->html_version);

		return $template;
	}
}
