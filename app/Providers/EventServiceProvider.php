<?php

namespace App\Providers;

use App\Events\ClassPublished;
use App\Listeners\SendEmailOnClassPublished;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\MemberJoined' => [
            'App\Listeners\EmailActivation',
        ],
        'App\Events\FriendInvited' => [
            'App\Listeners\EmailFriendInvite',
        ],
        'App\Events\InstructorInvitedMember' => [
            'App\Listeners\EmailInstructorInvite',
        ],
        'App\Events\AlertTriggered' => [
            'App\Listeners\InsertAlert',
        ],
        'App\Events\ClassCancelled' => [
            'App\Listeners\ProcessBookingCancellations',
        ],
        'App\Events\BookingMade' => [
            'App\Listeners\ProcessBooking',
        ],
        'App\Events\ClassComingUp' => [
            'App\Listeners\NotifyUserOfClass',
        ],
        ClassPublished::class => [
            SendEmailOnClassPublished::class,
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
