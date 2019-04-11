<?php

namespace App\Console\Commands;

use Carbon;
use ClassHelper;
use App\Models\Setting;
use App\Models\Booking;
use App\Models\ClassEvent;
use App\Models\ClassInvite;
use Illuminate\Console\Command;

class CalculateClassEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classes:earnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate class earnings for the day before.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ClassEvent $classevent, Booking $booking, Setting $setting, ClassInvite $classinvite)
    {
        parent::__construct();

        $this->classevent = $classevent;
        $this->booking = $booking;
        $this->setting = $setting;
        $this->classinvite = $classinvite;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $setting = $this->setting->findOrFail(1);
        $total_margin_percentage = $setting->margin_percentage;

        $yesterday = Carbon::parse('yesterday')->format('Y-m-d');
        $classes = $this->classevent
            ->where('class_at', 'LIKE', $yesterday . '%')
            ->where('status', 1)
            ->whereNull('cancelled_at')
            ->whereNull('totals_calculated_at')
            ->get();

        foreach ($classes as $class) {
            $bookings_data = [];
            $total_gross = 0;
            $total_attended = 0;
            $total_ratings_sum = 0;
            $total_ratings_made = 0;
            $total_rating = 0; // $total_ratings_sum / $total_ratings_made
            // Get bookings, add to array, write back to class

            $bookings = $this->booking->with('classUser')
                ->where('class_id', $class->id)
                ->where('status', 1)
                ->whereNull('refunded_at')
                ->get();

            foreach ($bookings as $booking) {
                $booking_info = ClassHelper::bookingCostAndType($booking, $class);
                $total_attended++;
                $total_gross += $booking_info['cost'];

                $bookings_data[] = [
                    'booking_id' => $booking->id,
                    'member_id' => $booking->user_id,
                    'member_name' => $booking->classUser->name,
                    'member_surname' => $booking->classUser->surname,
                    'fee_total' => $booking_info['cost'],
                    'fee_type' => $booking_info['type']
                ];

                if ($booking->rating > 0) {
                    $total_ratings_sum += $booking->rating;
                    $total_ratings_made++;
                }
            }

            $total_rating = $total_ratings_made > 0 ? ($total_ratings_sum / $total_ratings_made) : 0;

            // Invites info
            $invite_lists = [];
            $invite_total = 0;
            $invite_booked = 0;

            $invites = $this->classinvite->where('class_id', $class->id)->get();

            foreach ($invites as $invite) {
                $invite_total++;
                if ($invite->booked > 0) {
                    $invite_booked++;
                }
                if ($invite->list_id >= 0 && !isset($invite_lists[$invite->list_id])) {
                    $invite_lists[] = $invite->list_id;
                }
            }

            $class->total_gross = $total_gross;
            $class->total_margin_percentage = $total_margin_percentage;
            $class->total_attended = $total_attended;
            $class->class_data = serialize($bookings_data);
            $class->total_invited = $invite_total;
            $class->total_invited_booked = $invite_booked;
            $class->total_lists_used = count($invite_lists);
            $class->total_rating = $total_rating;
            $class->total_ratings = $total_ratings_made;
            $class->totals_calculated_at = Carbon::now();
            $class->save();
        }
    }
}
