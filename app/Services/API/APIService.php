<?php

namespace App\Services\API;

use App\Models\Setting;

abstract class APIService
{
	protected $model;

    public function max($field)
    {
        return $this->model->max($field);
    }

    public function getAllSettings()
    {
        $data = Setting::where('id', 1)->first();

        return $data;
    }
}
