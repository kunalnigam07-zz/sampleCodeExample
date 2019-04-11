<?php

namespace App\Console\Commands;

use Event;
use Carbon;
use DateHelper;
use AuthHelper;
use ClassHelper;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\ClassEvent;
use App\Events\ClassComingUp;
use Illuminate\Console\Command;
use App\Models\CommunicationType;

class NotificationsAndCancellations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'triggers:notifyandcancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger email notifications before class start/attend and trigger auto-cancellations.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ClassEvent $classevent)
    {
        parent::__construct();

        $this->classevent = $classevent;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->notifications();
        $this->autoCancellations(); 
    }

    // All classes for the next 25 hours NOT started - get minutes and send 1 of 3 emails OR SMSes
    public function notifications()
    {
        $classes = $this->classevent
            ->where('published', 1)
            ->where('status', 1)
            ->where('class_at', '>', Carbon::now())
            ->where('class_at', '<', Carbon::now()->addHours(25))
            ->whereNull('cancelled_at')
            ->get();

        $communication_types = CommunicationType::orderBy('id', 'ASC')->get();

        foreach ($classes as $class) {
            $diff = DateHelper::diffInMins(Carbon::now(), $class->class_at);

            // If class start within any of the 3 ranges
            if ($diff > 10 && $diff <= 1440) {
                $bookings = Booking::with('classUser')->where('class_id', $class->id)->whereNull('refunded_at')->where('status', 1)->get();

                // Members by booking
                foreach ($bookings as $booking) {
                    $comm_settings = AuthHelper::communicationSettings($booking->user_id, $communication_types);

                    if ($diff > 10 && $diff <= 15 && ($comm_settings[1]['email'] == 1 || $comm_settings[1]['sms'] == 1)) { // 15 mins
                        Event::fire(new ClassComingUp($booking->classUser, $class, 8, $comm_settings[1]));
                    }

                    if ($diff > 55 && $diff <= 60 && ($comm_settings[2]['email'] == 1 || $comm_settings[2]['sms'] == 1)) { // 1 hour
                        Event::fire(new ClassComingUp($booking->classUser, $class, 9, $comm_settings[2]));
                    }

                    if ($diff > 1435 && $diff <= 1440 && ($comm_settings[3]['email'] == 1 || $comm_settings[3]['sms'] == 1)) { // 24 hours
                        Event::fire(new ClassComingUp($booking->classUser, $class, 10, $comm_settings[3]));
                    }
                }

                // Instructor
                $comm_settings = AuthHelper::communicationSettings($class->user_id, $communication_types);

                if ($diff > 10 && $diff <= 15 && ($comm_settings[1]['email'] == 1 || $comm_settings[1]['sms'] == 1)) { // 15 mins
                    Event::fire(new ClassComingUp($class->classInstructor, $class, 11, $comm_settings[1]));
                }

                if ($diff > 55 && $diff <= 60 && ($comm_settings[2]['email'] == 1 || $comm_settings[2]['sms'] == 1)) { // 1 hour
                    Event::fire(new ClassComingUp($class->classInstructor, $class, 12, $comm_settings[2]));
                }

                if ($diff > 1435 && $diff <= 1440 && ($comm_settings[3]['email'] == 1 || $comm_settings[3]['sms'] == 1)) { // 24 hours
                    Event::fire(new ClassComingUp($class->classInstructor, $class, 13, $comm_settings[3]));
                }
            }
        }
    }

    // All classes that were supposed to start now. If actual start is null and start >= 10 (CMS value) mins, cancel it
    public function autoCancellations()
    {
        $setting = Setting::find(1);

        $classes = $this->classevent
            ->where('published', 1)
            ->where('status', 1)
            ->where('class_at', '<', Carbon::now()) // Past start time
            ->where('class_at', '>', Carbon::now()->subHours(1)) // Only include classes that should have started within 1 hour ago
            ->whereNull('cancelled_at') // Not yet cancelled
            ->whereNull('actual_start_at') // Not yet started
            ->get();

        foreach ($classes as $class) {
            $diff = DateHelper::diffInMins($class->class_at, Carbon::now());

            if ($diff > $setting->class_cancellation_mins) {
                ClassHelper::cancelClass($class->id);
            }
        }
    }
}
