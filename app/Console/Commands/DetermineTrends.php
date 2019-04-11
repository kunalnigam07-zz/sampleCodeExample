<?php

namespace App\Console\Commands;

use Event;
use Carbon;
use NumberHelper;
use App\Models\User;
use App\Models\Interest;
use App\Models\ClassEvent;
use App\Events\AlertTriggered;
use Illuminate\Console\Command;

class DetermineTrends extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classes:trends';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Determine trending classes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ClassEvent $classevent, User $user, Interest $interest)
    {
        parent::__construct();

        $this->classevent = $classevent;
        $this->user = $user;
        $this->interest = $interest;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /* For all classes get booking number set that:
            - were added at least 7 days ago 
            - start after tomorrow the earliest 
            - and not seen as trending
            - class > 10 members
           For each set, determine slope and grab those with slope > x
           #1 will be newsfeed #21 and #23
           #2 and #3 could be based on interest for newsfeed #20 and #22
        */
        $trend = 2.5; // If slope is higher or equal, it's trending
        $highest_slope = -1;
        $class_with_highest_slope = 0;
        $sets = [];
        $dates = [];

        for ($i = 7; $i > 0; $i--) {
            $dates[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }

        $classes = $this->classevent->with('bookings', 'classInstructor')
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->where('class_at', '>', Carbon::tomorrow())
            ->whereNull('trended_at')
            ->where('max_number', '>', 10)
            ->whereNull('cancelled_at')
            ->where('status', 1)
            ->where('published', 1)
            ->get();

        foreach ($classes as $class) {
            // Only bother if there is at least 1 booking
            if (count($class->bookings) > 0) {
                $sets[$class->id] = ['bookings' => [], 'slope' => -1, 'class' => $class];
                $running_total = 0;

                foreach ($dates as $date) {
                    $count = $class->bookings()->where('created_at', 'LIKE', $date . ' %')->count();
                    $running_total += $count;
                    $sets[$class->id]['bookings'][] = $running_total;
                }

                $slope = NumberHelper::leastSquareFit($sets[$class->id]['bookings']);

                if ($slope < $trend) {
                    unset($sets[$class->id]);
                } else {
                    $sets[$class->id]['slope'] = $slope;

                    if ($slope > $highest_slope) {
                        $highest_slope = $slope;
                        $class_with_highest_slope = $class->id;
                    }
                }
            }
        }

        // Update all trending classes in the array to set trended_at
        if (count($sets) > 0) {
            $this->classevent->whereIn('id', array_keys($sets))->update(['trended_at' => Carbon::now()]);
        }

        // Set global trending class for all
        if ($class_with_highest_slope > 0) {
            Event::fire(new AlertTriggered(21, ['class' => $sets[$class_with_highest_slope]['class'], 'instructor' => $sets[$class_with_highest_slope]['class']->classInstructor]));
            Event::fire(new AlertTriggered(23, ['class' => $sets[$class_with_highest_slope]['class'], 'instructor' => $sets[$class_with_highest_slope]['class']->classInstructor]));

            // Remove top class so they're not added to feed again
            unset($sets[$class_with_highest_slope]);
        }

        // Set other trending classes based on interests (members - #20) and class types (instructors - #22)
        foreach ($sets as $k => $set) {
            // Only work with classes that have level 2 category set
            if ($set['class']->type_2_id > 0) {
                $type = $set['class']->type_2_id;

                // Get all members with this class as their interest
                $interests = $this->interest->select('interests.user_id')
                    ->where('interests.class_type_id', $type)
                    ->join('users', 'interests.user_id', '=', 'users.id')
                    ->where('users.user_type', 3)
                    ->get();
                foreach ($interests as $interest) {
                    Event::fire(new AlertTriggered(20, ['user' => $interest->user_id, 'class' => $set['class'], 'instructor' => $set['class']->classInstructor]));
                }

                // Get all instructors with this in their calculated "class_types"
                $instructors = $this->user->instructors()
                    ->where('status', 1)
                    ->where('class_types', 'LIKE', '%_' . $type . '_%')
                    ->get();
                foreach ($instructors as $instructor) {
                    // Don't add if it's instructor's own class
                    if ($instructor->id != $set['class']->classInstructor->id) {
                        Event::fire(new AlertTriggered(22, ['user' => $instructor->id, 'class' => $set['class'], 'instructor' => $set['class']->classInstructor]));
                    }
                }
            }
        }
    }
}
