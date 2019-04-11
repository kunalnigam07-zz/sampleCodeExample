<?php

namespace App\Helpers;

use Event;
use Carbon;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Booking;
use App\Models\ClassType;
use App\Models\ClassEvent;
use App\Models\DiscountCode;
use App\Events\ClassCancelled;

class ClassHelper
{
	public static function bookingCostAndType($booking, $class)
	{
        // TYPES: (free - this is new!), single, codefree, codecancel, bulkone, bulkgroup
        $ret = ['type' => 'free', 'cost' => 0];

        if ($booking->order_id > 0) {
            $order = Order::find($booking->order_id);
            if (isset($order)) {
                if ($order->class_id > 0) {
                    $ret['type'] = 'single';
                    $ret['cost'] = $class->price;
                } elseif ($order->bulk_package_id > 0) {
                    $bulk_per_class_price = round($order->price / $order->bulk_qty, 2);
                    $ret['cost'] = $bulk_per_class_price;

                    if ($order->bulk_type == 0) {
                        $ret['type'] = 'bulkone';
                    } elseif ($order->bulk_type == 1) {
                        $ret['type'] = 'bulkgroup';
                    }
                }
            }
        } elseif ($booking->discount_code_id > 0) {
            $discount_code = DiscountCode::find($booking->discount_code_id);
            if (isset($discount_code)) {
                if ($discount_code->type == 1) { // Free use
                    $ret['type'] = 'codefree';
                    $ret['cost'] = 0;
                } elseif ($discount_code->type == 2) { // Cancellation
                    $ret['type'] = 'codecancel';
                    $ret['cost'] = $class->price;
                }
            }
        }

        return $ret;
	}

    public static function feeTypeFriendly($type)
    {
        $ret = '';

        switch ($type) {
            case '': // Blanks from before
            case 'free':
                $ret = 'Free';
                break;
            case 'single':
                $ret = 'Single';
                break;
            case 'codefree':
                $ret = 'Free Code';
                break;
            case 'codecancel':
                $ret = 'Cancel Code';
                break;
            case 'bulkone':
                $ret = 'Bulk 1-on-1';
                break;
            case 'bulkgroup':
                $ret = 'Bulk Group';
                break;
        }

        return $ret;
    }

    public static function level($level)
    {
        $levels = [1 => 'Beginner', 2 => 'Intermediate', 3 => 'Advanced'];

        return $levels[$level];
    }

    public static function bulkType($type)
    {
        $types = [0 => '1-on-1', 1 => 'Group'];

        return $types[$type];
    }

    public static function cost($amount)
    {
        return $amount == 0 ? 'FREE' : '&pound;' . $amount;
    }

    public static function costCurrency($amount, $currency, $currencies)
    {
        if ($amount == 0) {
            return ['FREE', '', 0]; // Display, currency code, amount
        } else {
            if (isset($currencies[$currency])) {
                $cur = $currencies[$currency];
                $rate = $cur->rate;

                // Don't allow rate to be less than the minimum
                if ($rate < $cur->rate_min) {
                    $rate = $cur->rate_min;
                }

                $rate += $cur->profit_rate;

                $final = round($amount * $rate, 2);

                return [$currencies[$currency]->symbol . $final, $currencies[$currency]->code, $final];
            } else {
                return ['&pound;' . $amount, 'GBP', $amount];
            }
        }
    }

    public static function classStatsInfo($class)
    {
        $ret = [
            'earned' => 0,
            'attended_invited_percentage' => 0
        ];

        $total_gross = $class->total_gross;
        $total_margin_percentage = $class->total_margin_percentage;

        if ($total_margin_percentage > 0) {
            $ret['earned'] = round($total_gross / 100 * (100 - $total_margin_percentage), 2);
        } else {
            $ret['earned'] = $total_gross;
        }

        $total_invited = $class->total_invited;
        $total_invited_booked = $class->total_invited_booked;

        if ($total_invited > 0) {
            $ret['attended_invited_percentage'] = round($total_invited_booked / $total_invited * 100, 0);
        } else {
            $ret['attended_invited_percentage'] = 0;
        }

        return $ret;
    }

    public static function classStatus($class, $button_template, $calculate_earnings = false)
    {
        $ret = [
            'full' => false, // Whether class is full
            'finished' => false, // Whether class has passed
            'started' => false, // Whether class has started
            'my_booking' => false, // Whether logged in member has a booking for this class
            'spaces_booked' => 0, // Number of spaces booked out
            'spaces_total' => $class->max_number, // Total spaces available
            'book_button' => '', // Booking button
            'earned' => 0, // Net amount earned after margins,
            'instructor_allowed_to_start' => false, // Whether instructor is allowed to start class
            'earned_so_far' => 0 // Net amount earned after margins (so far)
        ];

        if ($class->class_ends_at < Carbon::now() || $class->actual_end_at != null) {
            $ret['finished'] = true;
        }

        if ($class->class_at < Carbon::now() || $class->actual_start_at != null) {
            $ret['started'] = true;
        }

        $total_gross = $class->total_gross;
        $total_margin_percentage = $class->total_margin_percentage;

        if ($total_margin_percentage > 0) {
            $ret['earned'] = round($total_gross / 100 * (100 - $total_margin_percentage), 2);
        } else {
            $ret['earned'] = $total_gross;
        }

        // Do not do manual booking calculations if earnings have already been calculated
        $preliminary_earnings = 0;
        if ($ret['earned'] > 0) {
            $calculate_earnings = false;
            $ret['earned_so_far'] = $ret['earned'];
        }

        $bookings = Booking::where('class_id', $class->id)->whereNull('refunded_at')->where('status', 1)->get();
        foreach ($bookings as $booking) {
            $ret['spaces_booked']++;
            if (AuthHelper::isLoggedInMember() && $booking->user_id == AuthHelper::id()) {
                $ret['my_booking'] = true;
            }
            if ($calculate_earnings) {
                $booking_info = ClassHelper::bookingCostAndType($booking, $class);
                $preliminary_earnings += $booking_info['cost'];
            }
        }

        if ($calculate_earnings) {
            $setting = Setting::findOrFail(1);
            $total_mp = $setting->margin_percentage;

            if ($total_mp > 0) {
                $ret['earned_so_far'] = round($preliminary_earnings / 100 * (100 - $total_mp), 2);
            } else {
                $ret['earned_so_far'] = $preliminary_earnings;
            }
        }

        if ($ret['spaces_booked'] >= $class->max_number) {
            $ret['full'] = true;
        }

        if (!$ret['finished'] && $class->status == 1 && $class->published == 1 && self::withinClassTimeframe($class, config('app.start_class_before.instructor'))) {
            $ret['instructor_allowed_to_start'] = true;
        }

        // Determine button template

        switch ($button_template) {
            case 1: // web.partial-classes.discover
                $class_url = RouteHelper::getRoute('web.class.details', [$class->id, StringHelper::slug($class->title)]);

                if (AuthHelper::isLoggedInInstructor()) {
                    $ret['book_button'] = '<a href="' . $class_url . '" class="btn red-full">Class Details</a>';
                } else {
                    if ($ret['my_booking']) {
                        if (self::withinClassTimeframe($class, config('app.start_class_before.member'))) {
                            // $ret['book_button'] = '<a href="' . RouteHelper::getRoute('web.live', $class->id) . '?sc=1" class="btn red-full">Begin Class</a>  <i>' . ($class->class_at > Carbon::now() ? 'Starting in' : 'Started') . ' <span>' . DateHelper::showFriendlyDate($class->class_at) . '</span></i>';
                            $ret['book_button'] = '<a href="' . RouteHelper::getRoute('web.live', $class->id) . '?sc=1" class="btn green-full">Begin Class</a>  <i>' . ($class->class_at > Carbon::now() ? 'Starting in' : 'Started') . ' <span>' . DateHelper::showFriendlyDate($class->class_at) . '</span></i>';
                        } else {
                            $ret['book_button'] = '<i>' . ($class->class_at > Carbon::now() ? 'Starting in' : 'Started') . ' <span>' . DateHelper::showFriendlyDate($class->class_at) . '</span></i>';
                        }
                    } elseif ($ret['full']) {
                        $ret['book_button'] = '<a href="' . $class_url . '" class="btn red-full">Class Full</a>';
                    } elseif (/*!$ret['started'] && */!$ret['finished']) {
                        $ret['book_button'] = '<a href="' . RouteHelper::getRoute('web.book', $class->id) . '" class="btn red-full">Book Now</a>';
                    }
                }
                break;
            case 2: // web.class.index
                if (AuthHelper::isLoggedInInstructor()) {
                    $ret['book_button'] = '<p>Create a linked student account if you wish to book this class.</p>';
                    if (AuthHelper::id() == $class->user_id) {
                        if ($ret['spaces_booked'] == 0 && !$ret['finished']) {
                            $ret['book_button'] = '<a href="' . RouteHelper::getRoute('web.instructor.create', ['id' => $class->id]) . '" class="btn white-full stats-white">Edit Class</a>';
                        }
                        if (!$ret['started'] && $class->published == 0) {
                            $ret['book_button'] .= '<a href="' . RouteHelper::getRoute('web.instructor.publish', ['id' => $class->id]) . '" class="btn white-full stats-white">Publish</a>';
                        }
                    }
                } else {
                    $class_url = RouteHelper::getRoute('web.class.details', [$class->id, StringHelper::slug($class->title)]);

                    if ($ret['my_booking'] && self::withinClassTimeframe($class, config('app.start_class_before.member'))) {
                        $ret['book_button'] = '<a href="' . RouteHelper::getRoute('web.live', $class->id) . '?sc=1" class="btn white-full stats-white">Begin Class</a>';
                    } elseif ($ret['full']) {
                        $ret['book_button'] = '<a href="' . $class_url . '" class="btn white-full stats-white">Class Full</a>';
                    } elseif (/*!$ret['started'] && */!$ret['finished']) {
                        $ret['book_button'] = '<a href="' . RouteHelper::getRoute('web.book', $class->id) . '" class="btn white-full stats-white">Book Now</a>';
                    }
                }
                break;
        }

        return $ret;
    }

    public static function tags($types_string)
    {
        $ret = [];
        $ret_naked = [];
        $types_array = explode(',', $types_string);
        $types = ClassType::select('id', 'title')->where('status', 1)->lists('title', 'id')->all();

        foreach ($types_array as $v) {
            if (strlen($v) > 2) {
                $tmp = '';
                $tmp_naked = '';
                $type_array = explode('_', $v);
                $terms_counter = 0;

                if ($type_array[2] > 0) {
                    $terms_counter++;
                    $tmp .= '<a href="' . RouteHelper::getRoute('web.discover', ['kw' => $types[$type_array[2]]]) . '">' . $types[$type_array[2]] . '</a> ';
                    $tmp_naked .= $types[$type_array[2]] . ' ';
                }

                if ($type_array[1] > 0) {
                    $terms_counter++;
                    $tmp .= '<a href="' . RouteHelper::getRoute('web.discover', ['kw' => $types[$type_array[1]]]) . '">' . $types[$type_array[1]] . '</a> ';
                    $tmp_naked .= $types[$type_array[1]] . ' ';
                }

                if ($type_array[0] > 0 && $terms_counter <= 1) {
                    $tmp .= '<a href="' . RouteHelper::getRoute('web.discover', ['kw' => $types[$type_array[0]]]) . '">' . $types[$type_array[0]] . '</a> ';
                    $tmp_naked .= $types[$type_array[0]] . ' ';
                }

                $ret[] = trim($tmp);
                $ret_naked[] = trim($tmp_naked);
            }
        }

        return [implode(', ', $ret), implode(', ', $ret_naked)];
    }

    public static function cancelClass($id)
    {
        ClassEvent::where('id', $id)->update([
            'cancelled_at' => Carbon::now(),
            'published' => 0,
            'status' => 0
        ]);

        $bookings = Booking::with('classEvent.classInstructor', 'classUser')->where('class_id', $id)->whereNull('refunded_at')->where('status', 1)->get();
        foreach ($bookings as $booking) {
            Event::fire(new ClassCancelled($booking));
        }
    }

    // Member only has 24 hours to rate class after it's completed
    public static function withinRateTimeframe($class)
    {
        $mins = 0;
        $d = Carbon::parse($class->actual_end_at);
        $now = Carbon::now();
        
        $mins = $now->diffInMinutes($d);

        if ($mins <= (60 * 24)) { // 24 hours to rate
            return true;
        }

        return false;
    }

    public static function withinTimeframeBefore($class, $mins)
    {
        $diff = 0;
        $d = Carbon::parse($class->class_at);
        $now = Carbon::now();
        
        $diff = $now->diffInMinutes($d);

        if ($diff <= $mins && $now <= $d) {
            return true;
        }

        return false;
    }

    public static function withinTimeframeAfter($class, $mins)
    {
        $diff = 0;
        $d = Carbon::parse($class->class_at);
        $now = Carbon::now();
        
        $diff = $now->diffInMinutes($d);

        if ($diff <= $mins && $now >= $d) {
            return true;
        }

        return false;
    }

    public static function withinClassTimeframe($class, $mins)
    {
        $start = Carbon::parse($class->class_at)->subMinutes($mins);
        $end = Carbon::parse($class->class_ends_at);
        $now = Carbon::now();
        
        if (($now >= $start && $now <= $end->addMinutes(30))) {
            return true;
        }

        return false;
    }

    public static function icsHash($cid)
    {
        return sha1($cid . config('app.key') . 'VRksFzy8lINx2VtoIpNU');
    }
}
