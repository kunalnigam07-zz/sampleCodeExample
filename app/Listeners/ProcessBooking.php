<?php

namespace App\Listeners;

use DateHelper;
use EmailHelper;
use RouteHelper;
use ClassHelper;
use App\Models\Booking;
use App\Events\BookingMade;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessBooking
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Handle the event.
     *
     * @param  BookingMade  $event
     * @return void
     */
    public function handle(BookingMade $event)
    {
        $class = $event->class;
        $member = $event->member;
        $instructor = $class->classInstructor;

        $ics_link = RouteHelper::getRoute('web.ics', ['token' => $class->id . '-' . ClassHelper::icsHash($class->id)], true);

        // Email member
        $params = [
            'NAME' => $member->name,
            'CLASS_NAME' => $class->title,
            'CLASS_DATE' => DateHelper::showDateTZ($class->class_at, 'j M Y \a\t H:i', $member->timezone) . ' ' . DateHelper::friendlyTimezone($member->timezone),
            'INSTRUCTOR_NAME' => $instructor->name . ' ' . $instructor->surname,
            'LINK' => RouteHelper::getRoute('web.member.classes', [], true),
            'CALENDAR_LINK' => $ics_link
        ];
        
        if (substr($member->email, 0, 15) != 'testrtc-student') {
            EmailHelper::send(7, $member->email, $params);
        }

        // Email instructor
        $total_class_bookings = $this->booking->where('class_id', $class->id)->whereNull('refunded_at')->where('status', 1)->count();

        $params = [
            'NAME' => $instructor->name,
            'MEMBER' => $member->name . ' ' . $member->surname[0] . '.',
            'CLASS_NAME' => $class->title,
            'CLASS_DATE' => DateHelper::showDateTZ($class->class_at, 'j M Y \a\t H:i', $instructor->timezone) . ' ' . DateHelper::friendlyTimezone($instructor->timezone),
            'TOTAL' => $total_class_bookings,
            'LINK' => RouteHelper::getRoute('web.instructor.bookings', [$class->id], true),
            'CALENDAR_LINK' => $ics_link
        ];

        if ((substr($instructor->email, 0, 18) != 'testrtc-instructor') && ($instructor->email != 'tony.beim@danlo.co.uk')) {
            EmailHelper::send(14, $instructor->email, $params);
        }
    }
}
