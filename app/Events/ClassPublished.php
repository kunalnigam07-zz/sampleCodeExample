<?php

namespace App\Events;

class ClassPublished extends Event
{
    /* @var array */
    protected $classEvents;

    /* @var array */
    protected $dates;

    public function __construct(array $classEvents, array $dates)
    {
        $this->classEvents = $classEvents;
        $this->dates = $dates;
    }

    public function getClassEvents(): array
    {
        return $this->classEvents;
    }

    public function getDates(): array
    {
        return $this->dates;
    }
}
