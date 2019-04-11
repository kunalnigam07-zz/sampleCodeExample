<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Booking;

class DashboardService extends AdminService
{
    public function __construct(Carbon $datetime)
    {
        $this->datetime = $datetime;
    }

    public function getCharts()
    {
        $chart = [
            'chart1' => [[],[]],
            'chart2' => [[],[]],
            'chart3' => [[],[]],
            'chart4' => [[],[]]
        ];

        for ($i = 9; $i >= 0; $i--) {
            $date_db = $this->datetime->now()->subDays($i)->format('Y-m-d');
            $date_display = $this->datetime->now()->subDays($i)->format('j M');
            $count = User::members()->whereRaw('DATE(created_at) = ?', [$date_db])->count();
            $chart['chart1'][0][] = $date_display;
            $chart['chart1'][1][] = $count;
        }

        for ($i = 9; $i >= 0; $i--) {
            $date_db = $this->datetime->now()->subDays($i)->format('Y-m-d');
            $date_display = $this->datetime->now()->subDays($i)->format('j M');
            $count = User::instructors()->whereRaw('DATE(created_at) = ?', [$date_db])->count();
            $chart['chart2'][0][] = $date_display;
            $chart['chart2'][1][] = $count;
        }

        for ($i = 9; $i >= 0; $i--) {
            $date_db = $this->datetime->now()->subDays($i)->format('Y-m-d');
            $date_display = $this->datetime->now()->subDays($i)->format('j M');
            $count = Booking::whereRaw('DATE(created_at) = ?', [$date_db])->count();
            $chart['chart3'][0][] = $date_display;
            $chart['chart3'][1][] = $count;
        }

        for ($i = 9; $i >= 0; $i--) {
            $date_db = $this->datetime->now()->subDays($i)->format('Y-m-d');
            $date_display = $this->datetime->now()->subDays($i)->format('j M');
            $count = Order::whereRaw('DATE(created_at) = ?', [$date_db])->count();
            $chart['chart4'][0][] = $date_display;
            $chart['chart4'][1][] = $count;
        }

    	return response()->json($chart);
    }
}
