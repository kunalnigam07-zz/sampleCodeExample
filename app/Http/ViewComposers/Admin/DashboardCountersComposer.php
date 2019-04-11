<?php

namespace App\Http\ViewComposers\Admin;

use NumberHelper;
use App\Models\User;
use App\Models\Order;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DashboardCountersComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $circle_colours = ['red', 'yellow', 'blue', 'green'];
        $circle_data = [['MEMBER', 0], ['INSTRUCTOR', 0], ['BOOKING', 0], ['ORDER', 0]];
        $dashboard_counters = '<div class="circle-stats"><div class="fake-table"><div class="fake-table-cell">';

        $circle_data[0][1] = User::members()->count();
        $circle_data[1][1] = User::instructors()->count();
        $circle_data[2][1] = Booking::count();
        $circle_data[3][1] = Order::count();

        if ($circle_data[0][1] != 1) {
            $circle_data[0][0] .= 'S';
        }

        if ($circle_data[1][1] != 1) {
            $circle_data[1][0] .= 'S';
        }

        if ($circle_data[2][1] != 1) {
            $circle_data[2][0] .= 'S';
        }

        if ($circle_data[3][1] != 1) {
            $circle_data[3][0] .= 'S';
        }

        foreach ($circle_data as $k => $v) {
            $dashboard_counters .= '<div class="circle ' . $circle_colours[$k] . '"><div class="fake-table"><div class="fake-table-cell"><p class="counter">' . NumberHelper::friendlyNumber($v[1]) . '</p><span>' . $v[0] . '</span></div></div></div>';
        }

        $dashboard_counters .= '</div></div></div>';

        $view->with('dashboard_counters', $dashboard_counters);
    }
}
