<?php

namespace App\Helpers;

class NetworkHelper
{
	public static function getIP()
	{
		$ip = '';
		
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		if ($ip == '127.0.0.1') {
			$ip = '2a02:c7f:8e1a:5900:ccdd:1213:6055:362e';
		}
		
		return $ip;
	}
}
