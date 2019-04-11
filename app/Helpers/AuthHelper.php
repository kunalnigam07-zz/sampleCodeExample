<?php

namespace App\Helpers;

use Auth;
use Carbon;
use App\Models\Ban;
use App\Models\Feed;
use App\Models\Follow;
use App\Models\Newsfeed;
use App\Models\CommunicationType;
use App\Models\CommunicationSetting;

class AuthHelper
{
    public static function isLoggedInMemberOrInstructor()
    {
        if (Auth::check() && self::user()->user_type != 1) {
            return true;
        }

        return false;
    }

    public static function isLoggedInMember()
    {
        if (Auth::check() && self::user()->user_type == 3) {
            return true;
        }

        return false;
    }

    public static function isLoggedInInstructor()
    {
        if (Auth::check() && self::user()->user_type == 2) {
            return true;
        }

        return false;
    }

    public static function id()
    {
        return Auth::id();   
    }

    public static function user()
    {
        return Auth::user();   
    }

    public static function logout()
    {
        Auth::logout();
    }

    public static function check()
    {
        return Auth::check();
    }

    public static function isAdmin()
    {
        return self::user()->user_type == 1 ? true : false;
    }

    public static function renderFollowButton($instructor_id, $template)
    {
        $ret = '';

        if (self::isLoggedInMemberOrInstructor()) {
            if (self::isLoggedInMember()) {
                $follows = Follow::where('user_id', $instructor_id)->where('followed_by_id', self::id())->count();

                switch ($template) {
                    case 1:
                        if ($follows > 0) {
                            $ret = '<a href="#" class="unfollow follow-unfollow-profile" data-instructor="' . $instructor_id . '" data-template="1">Unfollow This Instructor</a>';
                        } else {
                            $ret = '<a href="#" class="follow follow-unfollow-profile" data-instructor="' . $instructor_id . '" data-template="1">Follow This Instructor</a>';
                        }
                        break;
                    case 2:
                        if ($follows > 0) {
                            $ret = '<a href="#" class="btn red unfollow follow-unfollow-profile" data-instructor="' . $instructor_id . '" data-template="2">Unfollow</a>';
                        } else {
                            $ret = '<a href="#" class="btn red follow follow-unfollow-profile" data-instructor="' . $instructor_id . '" data-template="2">Follow</a>';
                        }
                        break;
                }
                
            } else {
                // No options for instructors
            }
        } else {
            switch ($template) {
                case 1:
                    $ret = '<a href="#" class="follow" data-remodal-target="modal-login">Follow This Instructor</a>';
                    break;
                case 2:
                    // Nothing, as a non-logged in user will never see this render
                    break;
            }
        }

        return $ret;
    }

    public static function banCheck($type, $data)
    {
        $bans = Ban::where($type, $data)->count();

        if ($bans > 0) {
            return true;
        }

        return false;
    }

    public static function notificationCounts()
    {
        $totals = ['newsfeed' => 0, 'notifications' => 0];

        $newsfeed_last_at = Auth::user()->newsfeed_last_at;
        $notifications_last_at = Auth::user()->notifications_last_at;
        $dismissed_message_ids = Auth::user()->dismissed_message_ids;
        $dismissed_feed_ids = Auth::user()->dismissed_feed_ids;
        $user_type =  Auth::user()->user_type;

        if ($newsfeed_last_at == null) {
            $newsfeed_last_at = Auth::user()->created_at;
        }

        if ($notifications_last_at == null) {
            $notifications_last_at = Auth::user()->created_at;
        }

        if (strlen($dismissed_message_ids) > 0) {
            $dismissed_message_ids = unserialize($dismissed_message_ids);
        } else {
            $dismissed_message_ids = [0];
        }

        if (strlen($dismissed_feed_ids) > 0) {
            $dismissed_feed_ids = unserialize($dismissed_feed_ids);
        } else {
            $dismissed_feed_ids = [0];
        }

        /* Get number of newsfeed/notification articles  where:
           - is for the logged in user type
           - not in dismissed IDs
           - after last access date for that section
        */

        $results = Newsfeed::select('section_id')
            ->where('status', 1)
            ->whereIn('for_users', [0, $user_type])
            ->whereNotIn('id', $dismissed_message_ids)
            ->where(function ($query) use ($newsfeed_last_at, $notifications_last_at) {
                $query->where(function ($query) use ($newsfeed_last_at) {
                    $query->where('section_id', 1);
                        //->where('created_at', '>', $newsfeed_last_at);
                })->orWhere(function ($query) use ($notifications_last_at) {
                    $query->where('section_id', 2);
                        //->where('created_at', '>', $notifications_last_at);
                });
            })
            ->get();

        foreach ($results as $result) {
            switch ($result->section_id) {
                case 1:
                    $totals['newsfeed']++;
                    break;
                case 2:
                    $totals['notifications']++;
                    break;
            }
        }

        /* Get number of newsfeed/notifications  where:
           - is for the logged in user type 
           - is for that specific user (or all) 
           - not expired 
           - not in dismissed IDs 
           - after last access date for that section
        */

        $results = Feed::with('feedType')
            ->select('feed.id', 'feed_types.for_users', 'feed_types.section_id', 'feed.type_id')
            ->join('feed_types', 'feed.type_id', '=', 'feed_types.id')
            ->whereIn('feed_types.for_users', [0, $user_type])
            ->whereIn('feed.user_id', [0, Auth::id()])
            ->where('feed.expires_at', '>', Carbon::now())
            ->whereNotIn('feed.id', $dismissed_feed_ids)
            ->where(function ($query) use ($newsfeed_last_at, $notifications_last_at) {
                $query->where(function ($query) use ($newsfeed_last_at) {
                    $query->where('feed_types.section_id', 1);
                        //->where('feed.created_at', '>', $newsfeed_last_at);
                })->orWhere(function ($query) use ($notifications_last_at) {
                    $query->where('feed_types.section_id', 2);
                        //->where('feed.created_at', '>', $notifications_last_at);
                });
            })
            ->get();

        $types_no_16 = [];
        foreach ($results as $result) {
            switch ($result->section_id) {
                case 1:
                    $totals['newsfeed']++;
                    break;
                case 2:
                    if ($result->type_id == 16) {
                        if (count($types_no_16) == 0) {
                            $totals['notifications']++;
                        }
                        $types_no_16[] = $result->id;
                    } else {
                        $totals['notifications']++;
                    }
                    break;
            }
        }

        return $totals;
    }

    public static function instructorMayCreateClass()
    {
        $result = false;
        $dismissed_feed_ids = Auth::user()->dismissed_feed_ids;

        if (strlen($dismissed_feed_ids) > 0) {
            $dismissed_feed_ids = unserialize($dismissed_feed_ids);
        } else {
            $dismissed_feed_ids = [0];
        }

        $counter = 0;
        $feed_checks = [3, 4];

        foreach ($feed_checks as $v) {
            if (in_array($v, $dismissed_feed_ids)) {
                $counter++;
            }
        }

        if ($counter == count($feed_checks)) {
            $result = true;
        }

        // If not specified insurance and accreditations
        if (Auth::user()->has_insurance != 1 || Auth::user()->has_accreditations != 1) {
            $result = false;
        }

        return $result;
    }

    public static function doneSystemChecks()
    {
        if (self::isLoggedInMember()) {
            $feed_checks = 1;
        } elseif (self::isLoggedInInstructor()) {
            $feed_checks = 3;
        }

        $dismissed_feed_ids = Auth::user()->dismissed_feed_ids;

        if (strlen($dismissed_feed_ids) > 0) {
            $dismissed_feed_ids = unserialize($dismissed_feed_ids);
        } else {
            $dismissed_feed_ids = [];
        }

        if (in_array($feed_checks, $dismissed_feed_ids)) {
            return true;
        }

        return false;
    }

    public static function communicationSettings($member_id = 0, $communication_types = null)
    {
        $communication_settings = [];
        $user_id = $member_id > 0 ? $member_id : AuthHelper::id();

        if ($communication_types == null) {
            $communication_types = CommunicationType::orderBy('id', 'ASC')->get();
        }

        $selected = CommunicationSetting::where('user_id', $user_id)->get();

        if (count($selected) > 0) {
            foreach ($selected as $sel) {
                $communication_settings[$sel->type_id] = ['email' => 0, 'sms' => 0];
                if ($sel->email == 1) {
                    $communication_settings[$sel->type_id]['email'] = 1;
                }
                if ($sel->sms == 1) {
                    $communication_settings[$sel->type_id]['sms'] = 1;
                }
            }
        } else {
            foreach ($communication_types as $communication_type) {
                if ($communication_type->id == 6) {
                    $opt_email = 0;
                    $opt_sms = 0;
                } else {
                    $opt_email = 1;
                    $opt_sms = 0;
                }

                CommunicationSetting::create([
                    'user_id' => $user_id,
                    'type_id' => $communication_type->id,
                    'email' => $opt_email,
                    'sms' => $opt_sms
                ]);
                
                $communication_settings[$communication_type->id] = ['email' => $opt_email, 'sms' => $opt_sms];
            }
        }

        return $communication_settings;
    }

    // Used to hiden certain fields in forms
    public static function isWL()
    {
        // If it's plain not a white label, return false
        if (!config('app.white_label_settings.is_wl')) {
            return false;
        // If it's a white label, but the admin is ID #1 (Tony/Darren), set as false too so nothing is hidden
        } elseif (config('app.white_label_settings.is_wl') && Auth::id() == 1) {
            return false;
        }

        // All else fails, it's a white label, so some items will be hidden
        return true;
    }

    // Used to hide certain menu items from WL
    public static function canShowWLMenu($permission)
    {
        // If it's plain not a white label, return true
        if (!config('app.white_label_settings.is_wl')) {
            return true;
        // If it's a white label, but the admin is ID #1 (Tony/Darren), set as true too so nothing is hidden
        } elseif (config('app.white_label_settings.is_wl') && Auth::id() == 1) {
            return true;
        }

        if (in_array($permission, config('app.white_label_settings.hide_menus'))) {
            return false;
        } else {
            return true;
        }
    }
}
