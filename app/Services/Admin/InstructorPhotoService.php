<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Models\InstructorPhoto;
use Yajra\Datatables\Datatables;

class InstructorPhotoService extends AdminService
{
    protected $folder = 'instructorphotos';
    protected $columns = [
        'cols' => ['instructors_photos.id', 'instructors_photos.img', 'users.name', 'users.surname', 'instructors_photos.ordering', 'instructors_photos.updated_at', 'instructors_photos.status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(InstructorPhoto $model, UploadHelper $filesystem)
    {
        $this->model = $model;
        $this->filesystem = $filesystem;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\InstructorPhotoController@create')]],
            'ajax' => route('photos.dt.list'),
            'labels' => ['ID', 'Image', 'First Name', 'Last Name', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = InstructorPhoto::select($this->columns['cols'])->join('users', 'instructors_photos.user_id', '=', 'users.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\InstructorPhotoController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\InstructorPhotoController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->editColumn('img', '<img src="{{ AssetHelper::file(\'instructorphotos\', $id, $img, \'100x0\') }}" alt="" title="">')
            ->escapeColumns([2, 3])
            ->make();
    }

    public function editEntry($data, $id)
    {
        $fields = $data->except('_token', 'img', 'delete-img');
      
        if ($data->hasFile('img')) {
            $fields['img'] = $this->filesystem->upload('img', $data, $this->folder, $id);
        }

        if ($data->has('delete-img')) {
            $fields['img'] = '';
        }
        
        $this->model->where('id', $id)->update($fields);

        return true;
    }

    public function createEntry($data)
    {
        $fields = $data->except('_token', 'img', 'delete-img');
        
        $new = $this->model->create($fields);

        if ($data->hasFile('img')) {
            $new->img = $this->filesystem->upload('img', $data, $this->folder, $new->id);
            $new->save();
        }

        if ($data->has('delete-img')) {
            $new->img = '';
            $new->save();
        }

        return true;
    }

    public function deleteEntry($id)
    {
        $this->filesystem->deleteDirectory($this->folder, $id);

        return $this->model->destroy($id);
    }
}
