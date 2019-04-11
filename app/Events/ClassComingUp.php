<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ClassComingUp extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $class, $template_id, $comm_settings)
    {
        $this->user = $user;
        $this->class = $class;
        $this->template_id = $template_id;
        $this->comm_settings = $comm_settings;
    }
}
