<?php

namespace App\Http\ViewComposers\Web;

use Auth;
use AuthHelper;
use App\Models\UserLink;
use Illuminate\Contracts\View\View;

class UserComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $auth_user = Auth::user();
        $switch_to_instructor = false;
        $notification_counts = ['newsfeed' => 0, 'notifications' => 0];

        if (AuthHelper::isLoggedInMember()) {
            $switch_to_instructor = UserLink::where('member_id', $auth_user->id)->count() > 0 ? true : false;
        }

        if (AuthHelper::isLoggedInMemberOrInstructor()) {
            $notification_counts = AuthHelper::notificationCounts();
        }

        $view->with('auth_user', $auth_user)->with('switch_to_instructor', $switch_to_instructor)->with('notification_counts', $notification_counts);
    }
}
