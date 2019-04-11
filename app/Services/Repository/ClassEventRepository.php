<?php

namespace App\Services\Repository;

use App\Models\ClassEvent;
use Illuminate\Database\Eloquent\Builder;

class ClassEventRepository
{
    /* @var ClassEvent */
    private $model;

    public function __construct(ClassEvent $model)
    {
        $this->model = $model;
    }

    public function countByRangeOfClassAt(\DateTime $from, \DateTime $to): int
    {
        return $this->createQb()
            ->where('class_at', '>=', $from)
            ->where('class_at', '<=', $to)
            ->count();
    }

    protected function createQb(): Builder
    {
        return $this->model->newQuery();
    }
}
