<?php

namespace App\Console\Commands;

use DB;
use Event;
use Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\ClassEvent;
use App\Models\DiscountCode;
use App\Events\AlertTriggered;
use Illuminate\Console\Command;

class AlertsMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate notifications and newsfeed items that occur monthly.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DiscountCode $discountcode, Order $order, User $user, ClassEvent $classevent)
    {
        parent::__construct();

        $this->discountcode = $discountcode;
        $this->order = $order;
        $this->user = $user;
        $this->classevent = $classevent;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->alert3();
        $this->alert4();
        $this->alert6and7and15();
    }

    // Type #3: Member - Notification - Credits
    public function alert3()
    {
        $data = $this->discountcode->select('user_id', DB::raw('COUNT(id) AS creds'))
            ->has('redeem', '=', 0) // Not yet redeemed
            ->where('discount_codes.type', 2)
            ->where('discount_codes.status', 1)
            ->where('user_id', '>', 0)
            ->where('discount_codes.ends_at', '>=', Carbon::now())
            ->having('creds', '>', 0)
            ->groupBy('user_id')
            ->get();

        foreach ($data as $v) {
            Event::fire(new AlertTriggered(3, $v));
        }
    }

    // Type #4: Member - Notification - Bulk Classes
    public function alert4()
    {
        $users = [];

        $data = $this->order
            ->paidOrder()
            ->where('bulk_package_id', '>', 0)
            ->where('bulk_expires_at', '>=', Carbon::now())
            ->get();

        foreach ($data as $v) {
            if (!in_array($v->user_id, $users)) {
                $total_taken = $v->totalBulkUsed;

                if ($total_taken < $v->bulk_qty) {
                    $users[] = $v->user_id;
                    Event::fire(new AlertTriggered(4, $v));
                }
            }
        }
    }

    // Type #6: Instructor - Notification - Trained Members
    // Type #7: Instructor - Notification - Held Classes
    // Type #15: Instructor - Notification - Latest Stats
    public function alert6and7and15()
    {
        $data = $this->user->instructors()->where('status', 1)->where('members_trained', '>', 0)->where('classes_held', '>', 0)->get();

        foreach ($data as $v) {
            Event::fire(new AlertTriggered(6, $v));
            Event::fire(new AlertTriggered(7, $v));
            
            // Get last month's stats
            $stats = ['trained' => 0, 'earned' => 0];

            $classes = $this->classevent->where('user_id', $v->id)
                ->where('published', 1)
                ->where('status', 1)
                ->where('class_at', 'LIKE', Carbon::parse('last month')->format('Y-m') . '-%') // last month
                ->get();

            foreach ($classes as $class) {
                $total_gross = $class->total_gross;
                $total_margin_percentage = $class->total_margin_percentage;
                $total_attended = $class->total_attended;

                if ($total_margin_percentage > 0) {
                    $total_net = round($total_gross / 100 * (100 - $total_margin_percentage), 2);
                } else {
                    $total_net = $total_gross;
                }

                $stats['trained'] += $total_attended;
                $stats['earned'] += $total_net;
            }

            if ($stats['trained'] > 0) {
                Event::fire(new AlertTriggered(15, ['user' => $v->id, 'trained' => $stats['trained'], 'earned' => $stats['earned']]));
            }
        }
    }
}
