<?php

namespace App\Console\Commands;

use Carbon;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Console\Command;

class CalculateInstructorStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instructors:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate stats for instructors.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user, Booking $booking)
    {
        parent::__construct();

        $this->user = $user;
        $this->booking = $booking;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $instructors_array = [];

        $instructors = $this->user->instructors()->where('status', 1)->get();
        $bookings = $this->booking->with('classEvent')->where('status', 1)->get();

        foreach ($instructors as $instructor) {
            $instructors_array[$instructor->id] = [0, 0, [], [], []]; // Sum, # of Ratings, Members Trained, Classes Held, Class Types
        }

        foreach ($bookings as $booking) {
            if (isset($booking->classEvent) && $booking->classEvent->class_at < Carbon::now()) {
                $uid = $booking->classEvent->user_id;

                // If it was rated, add to the average
                if ($booking->rating > 0) {
                    $instructors_array[$uid][0] += $booking->rating;
                    $instructors_array[$uid][1]++;
                }

                // Determine number of unique members trained
                if (!in_array($booking->user_id, $instructors_array[$uid][2])) {
                    $instructors_array[$uid][2][] = $booking->user_id;
                }

                // Determine number of unique classes held
                if (!in_array($booking->class_id, $instructors_array[$uid][3])) {
                    $instructors_array[$uid][3][] = $booking->class_id;

                    // Add class types to array
                    $tstring = $booking->classEvent->type_1_id . '_' . $booking->classEvent->type_2_id . '_' . $booking->classEvent->type_3_id;
                    if (isset($instructors_array[$uid][4][$tstring])) {
                        $instructors_array[$uid][4][$tstring]++;
                    } else {
                        $instructors_array[$uid][4][$tstring] = 1;
                    }
                }
            }
        }

        foreach ($instructors_array as $k => $v) {
            // Determine top 3 class type strings
            $types = [];
            $count = 0;
            arsort($instructors_array[$k][4]);
            foreach ($instructors_array[$k][4] as $x => $y) {
                $count++;
                if ($count <= 3) {
                    $types[] = $x;
                }
            }

            $this->user->where('id', $k)->update([
                'rating' => $v[1] > 0 ? ($v[0] / $v[1]) : 0, // Prevent division by 0 error
                'ratings' => $v[1], 
                'members_trained' => count($v[2]),
                'classes_held' => count($v[3]),
                'class_types' => implode(',', $types),
                'stats_at' => Carbon::now()
            ]);
        }
    }
}
