<?php

namespace App\Helpers;

class RouteHelper
{
	public static function getRoute($route, $params = [], $absolute = false)
	{
		return route($route, $params, $absolute);
	}

    public static function userEditRoute($user)
    {
        $ret = '';

        if ($user->user_type == 2) {
            $ret = action('Admin\InstructorController@edit', $user->id);
        } elseif ($user->user_type == 3) {
            $ret = action('Admin\MemberController@edit', $user->id);
        }

        return $ret;
    }
}
