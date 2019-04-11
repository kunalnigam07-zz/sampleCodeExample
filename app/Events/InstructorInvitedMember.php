<?php

namespace App\Events;

use App\Events\Event;
use App\Models\ClassInvite;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InstructorInvitedMember extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ClassInvite $classinvite, $instructor)
    {
        $this->classinvite = $classinvite;
        $this->instructor = $instructor;
    }
}
