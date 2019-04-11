<?php

namespace App\Services\Admin;

use App\Models\Setting;
use Yajra\Datatables\Datatables;

class FeatureSettingService extends AdminService
{
    public function __construct(Setting $model)
    {
        $this->model = $model;
    }
}
