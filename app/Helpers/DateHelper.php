<?php

namespace App\Helpers;

use Auth;
use Carbon;

class DateHelper
{
	public static function showDate($date, $format = 'Y-m-d H:i:s')
	{
		if (strlen($date) > 0) {
			$ret = $date;
		} else {
			$ret = Carbon::now();
		}
		
		if (Auth::check()) {
			$ret = Carbon::parse($date)->setTimezone(Auth::user()->timezone)->format($format);
		} else {
			$ret = Carbon::parse($date)->format($format);
		}

		return $ret;
	}

    public static function showDateTZ($date, $format = 'Y-m-d H:i:s', $tz)
    {
        $ret = Carbon::parse($date)->setTimezone($tz)->format($format);
       
        return $ret;
    }

    public static function showDateTZDynamic($date, $format = 'Y-m-d H:i:s')
    {
        $ret = $date;
        
        if (Auth::check()) {
            $ret = Carbon::parse($date)->setTimezone(Auth::user()->timezone)->format($format);
        } elseif (session()->has('Visitor_Timezone') && strlen(session()->get('Visitor_Timezone')) > 1) {
            $ret = Carbon::parse($date)->setTimezone(session()->get('Visitor_Timezone'))->format($format);
        } else {
            $ret = Carbon::parse($date)->format($format);
        }

        return $ret;
    }

    public static function friendlyTimezone($timezone = '')
    {
        //Carbon::now($timezone)->format('T') gets the abbreviation, e.g. PST, etc.
        if ($timezone == '') {
            // See if logged in user and show accordingly, if not logged in timezone is UTC
            $timezone = AuthHelper::isLoggedInMemberOrInstructor() ? AuthHelper::user()->timezone : 'UTC';

            // If not logged in (so UTC) BUT session for timezone set, use that instead
            if ($timezone == 'UTC' && session()->has('Visitor_Timezone') && strlen(session()->get('Visitor_Timezone')) > 1) {
                $timezone = session()->get('Visitor_Timezone');
            }
        }
    
        $gmt_value = 'GMT';
        if ($timezone != 'UTC') {
            /*$offset_hours = Carbon::now($timezone)->offsetHours;

            if ($offset_hours != 0) {
                $gmt_value = 'GMT' . ($offset_hours > 0 ? '+' . $offset_hours : $offset_hours);
            }*/
            $gmt_value = Carbon::now($timezone)->format('T');
        }

        return $gmt_value;
    }

	public static function showFriendlyDate($date)
	{
		if (Auth::check()) {
			$ret = Carbon::parse($date)->setTimezone(Auth::user()->timezone)->diffForHumans();
		} else {
			$ret = Carbon::parse($date)->diffForHumans();
		}

		return $ret;
	}

    public static function addMinutes($num, $date)
    {
        $format = 'Y-m-d H:i:s';
        
        if (Auth::check()) {
            $ret = Carbon::parse($date)->setTimezone(Auth::user()->timezone)->addMinutes($num)->format($format);
        } else {
            $ret = Carbon::parse($date)->addMinutes($num)->format($format);
        }

        return $ret;
    }

    public static function addDays($num, $date)
    {
        $format = 'Y-m-d H:i:s';
        
        if (Auth::check()) {
            $ret = Carbon::parse($date)->setTimezone(Auth::user()->timezone)->addDays($num)->format($format);
        } else {
            $ret = Carbon::parse($date)->addDays($num)->format($format);
        }

        return $ret;
    }

    public static function excel($date, $time, $dateformat)
    {
        // Convert a modified Excel date back to usable format
        $ret = '';
        $final_date = $date;
        $final_time = $time;

        switch ($dateformat) {
            case 1:
                // 06/21/2016
                $temp = explode('/', $date);
                $final_date = $temp[2] . '-' . self::padZero($temp[0]) . '-' . self::padZero($temp[1]);
                break;
            case 2:
                // 21/06/2016
                $temp = explode('/', $date);
                $final_date = $temp[2] . '-' . self::padZero($temp[1]) . '-' . self::padZero($temp[0]);
                break;
            case 3:
                // 2016-06-21
                $temp = explode('-', $date);
                $final_date = $temp[0] . '-' . self::padZero($temp[1]) . '-' . self::padZero($temp[2]);
                break;
        }

        $temp = explode(':', $time);
        if (count($temp) == 2) {
            $final_time = $time . ':00';
        }

        $ret = $final_date . ' ' . $final_time;

        return $ret;
    }

    public static function padZero($num)
    {
        if (strlen('' . $num) == 1) {
            return '0' . $num;
        } else {
            return $num;
        }
    }

    public static function duration($from, $to)
    {
        $duration = round((strtotime($to) - strtotime($from)) / 60);

        if ($duration <= 0) {
            $duration = '';
        }

        return $duration;
    }

    public static function diffInMins($from, $to)
    {
        $duration = round((strtotime($to) - strtotime($from)) / 60);

        return $duration;
    }

    public static function getDates($limit, $repeat, $repeaton, $from, $to, $time)
    {
        $counter = 0;
        $add_days = -1;
        $utc_dates = [];

        if ($repeat == 'd') {
            do {
                $counter++;
                $add_days++;
                $date_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $from . ' ' . $time . ':00', Auth::user()->timezone)->addDays($add_days)->setTimezone('UTC');

                if ($date_carbon > Carbon::parse($to . ' 23:59:59')) {
                    break;
                }

                $utc_dates[] = $date_carbon;
            } while ($counter < $limit);
        } elseif ($repeat == 'w') {
            $date_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $from . ' 00:00:00', Auth::user()->timezone)->setTimezone('UTC');

            $repeater_days = [];
            for ($i = 1; $i <= $limit; $i++) {
                foreach ($repeaton as $v) {
                    $repeater_days[] = $v;
                    if (count($repeater_days) >= $limit) {
                        break;
                    }
                }
                if (count($repeater_days) >= $limit) {
                    break;
                }
            }

            foreach ($repeater_days as $v) {
                
                $date_carbon = $date_carbon->modify('next ' . $v);

                if ($date_carbon > Carbon::parse($to . ' 23:59:59')) {
                    break;
                }

                $date_r = Carbon::createFromFormat('Y-m-d H:i:s', $date_carbon->format('Y-m-d') . ' ' . $time . ':00', Auth::user()->timezone)->setTimezone('UTC') ;

                $utc_dates[] = $date_r;
            }
        }

        return $utc_dates;
    }
}
