<?php

namespace App\Console\Commands;

use Event;
use Carbon;
use DateHelper;
use ClassHelper;
use App\Models\ClassEvent;
use App\Events\AlertTriggered;
use Illuminate\Console\Command;

class AlertsDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate notifications and newsfeed items that occur daily.';

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
        $this->alert14();
        $this->alert17();
    }

    // Type #14: Instructor - Notification - Invite
    public function alert14()
    {
        $classes = $this->classevent
            ->where('published', 1)
            ->where('status', 1)
            ->where('class_at', 'LIKE', Carbon::now()->addDays(7)->format('Y-m-d') . '%') // in 7 days
            ->get();

        foreach ($classes as $class) {
            // Only if it has spaces left
            $status = ClassHelper::classStatus($class, 0);
            $left = $status['spaces_total'] - $status['spaces_booked'];

            if ($left > 0) {
                Event::fire(new AlertTriggered(14, [
                    'user' => $class->user_id,
                    'cid' => $class->id,
                    'class' => $class->title,
                    'date' => DateHelper::showDate($class->class_at, 'j M Y'),
                    'total' => $left
                ]));
            }
        }
    }

    // Type #17: Instructor - Notification - Average Star Rating
    public function alert17()
    {
        $classes = $this->classevent
            ->where('published', 1)
            ->where('status', 1)
            ->where('class_at', 'LIKE', Carbon::yesterday()->format('Y-m-d') . '%')
            ->where('total_rating', '>', 0)
            ->get();

        foreach ($classes as $class) {
            Event::fire(new AlertTriggered(17, [
                'user' => $class->user_id,
                'rating' => $class->total_rating,
                'class' => $class->title
            ]));
        }
    }
}
