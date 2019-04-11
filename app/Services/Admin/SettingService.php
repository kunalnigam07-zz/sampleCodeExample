<?php

namespace App\Services\Admin;

use UploadHelper;
use App\Models\Setting;
use Yajra\Datatables\Datatables;

class SettingService extends AdminService
{
    protected $folder = 'theme';

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function editEntry($data, $id)
    {
        $fields = $data->except('_token', 'logo', 'delete-logo', 'logo_og', 'delete-logo_og', 'logo_live', 'delete-logo_live', 'css_file', 'delete-css_file', 'logo_mobile', 'delete-logo_mobile');
      
        if ($data->hasFile('logo')) {
            $fields['logo'] = UploadHelper::upload('logo', $data, $this->folder, $id);
        }

        if ($data->hasFile('logo_og')) {
            $fields['logo_og'] = UploadHelper::upload('logo_og', $data, $this->folder, $id);
        }

        if ($data->hasFile('logo_live')) {
            $fields['logo_live'] = UploadHelper::upload('logo_live', $data, $this->folder, $id);
        }

        if ($data->hasFile('logo_mobile')) {
            $fields['logo_mobile'] = UploadHelper::upload('logo_mobile', $data, $this->folder, $id);
        }

        if ($data->hasFile('css_file')) {
            $fields['css_file'] = UploadHelper::upload('css_file', $data, $this->folder, $id);
        }

        if ($data->has('delete-logo')) {
            $fields['logo'] = '';
        }

        if ($data->has('delete-logo_og')) {
            $fields['logo_og'] = '';
        }

        if ($data->has('delete-logo_live')) {
            $fields['logo_live'] = '';
        }

        if ($data->has('delete-logo_mobile')) {
            $fields['logo_mobile'] = '';
        }

        if ($data->has('delete-css_file')) {
            $fields['css_file'] = '';
        }
        
        $this->model->where('id', $id)->update($fields);

        return true;
    }
}
