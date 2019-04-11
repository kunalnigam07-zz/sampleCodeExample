<?php

namespace App\Listeners;

use Carbon;
use DateHelper;
use EmailHelper;
use RouteHelper;
use StringHelper;
use App\Models\Order;
use App\Models\DiscountCode;
use App\Events\ClassCancelled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessBookingCancellations
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DiscountCode $discountcode, Order $order)
    {
        $this->discountcode = $discountcode;
        $this->order = $order;
    }

    /**
     * Handle the event.
     *
     * @param  ClassCancelled  $event
     * @return void
     */
    public function handle(ClassCancelled $event)
    {
        $booking = $event->booking;
        $member = $booking->classUser;
        $class = $booking->classEvent;
        $instructor = $booking->classEvent->classInstructor;

        // Check that:
        // 1. Only members who PAID or used a CANCELLATION code will receive a credit
        // 2. If BULK was used, it will simply be available again once booking is set as inactive
        // 3. If FREE CODE used, it's simply a lost booking

        $will_receive_credit = false;

        if ($booking->order_id > 0) {
            $order = $this->order->where('id', $booking->order_id)->first();
            if ($order->class_id > 0 && $order->price > 0) { // Check for price, in case it's a free class in which case no refund is needed
                $will_receive_credit = true;
            }
        }

        if ($booking->discount_code_id > 0) {
            $discount = $this->discountcode->where('id', $booking->discount_code_id)->first();
            if ($discount->type == 2) {
                $will_receive_credit = true;
            }
        }

        $booking->refunded_at = Carbon::now();
        $booking->status = 0;
        $booking->save();

        if ($will_receive_credit) {
            $code = StringHelper::generateDiscountCode();

            $this->discountcode->create([
                'title' => 'Class Cancelled', 
                'code' => $code,
                'type' => 2,
                'email' => '',
                'starts_at' => Carbon::now(),
                'ends_at' => DateHelper::addDays(90, Carbon::now()),
                'instructor_id' => $class->user_id,
                'user_id' => $booking->user_id,
                'cancelled_booking_id' => $booking->id,
                'class_max_number' => $class->max_number,
                'notes' => 'Cancellation of class #' . $class->id . '.',
                'status' => 1
            ]);
        }

        $params = [
            'NAME' => $member->name,
            'CLASS_NAME' => $class->title,
            'CLASS_DATE' => DateHelper::showDateTZ($class->class_at, 'd F Y', $member->timezone) . ' ' . DateHelper::friendlyTimezone($member->timezone),
            'CLASS_INSTRUCTOR' => $instructor->name . ' ' . $instructor->surname,
            'LINK' => RouteHelper::getRoute('web.member.credits', [], true)
        ];
              
        if (substr($member->email, 0, 15) != 'testrtc-student') {
            EmailHelper::send(5, $member->email, $params);
        }      
    }
}
