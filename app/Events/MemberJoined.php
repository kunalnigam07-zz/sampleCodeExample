<?php

namespace App\Events;

use App\Models\User;
use App\Events\Event;
use App\Models\Setting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MemberJoined extends Event
{
    use SerializesModels;

    public $user;
    public $settings;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Setting $settings)
    {
        $this->user = $user;
        $this->settings = $settings;
    }
}
