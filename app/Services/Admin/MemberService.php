<?php

namespace App\Services\Admin;

use Auth;
use Hash;
use Carbon;
use DateHelper;
use UploadHelper;
use App\Models\User;
use App\Models\Import;
use Maatwebsite\Excel\Excel;
use Yajra\Datatables\Datatables;

class MemberService extends AdminService
{
    protected $folder = 'members';
    protected $columns = [
        'cols' => ['id', 'name', 'surname', 'email', 'created_at', 'status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(User $model, Excel $excel, Import $import)
    {
        $this->model = $model;
        $this->excel = $excel;
        $this->import = $import;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\MemberController@create')], ['Export', 'file-excel-o', action('Admin\MemberController@showExport')], ['Import', 'file-text-o', action('Admin\MemberController@showImport')]],
            'ajax' => route('members.dt.list'),
            'labels' => ['ID', 'First Name', 'Last Name', 'Email', 'Registration Date', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = $this->model->members()->select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\MemberController@loginAs\', $id) }}" class="login"><i class="fa fa-lock"></i></a>
                <a href="{{ action(\'Admin\MemberController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\MemberController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('created_at', '{{ DateHelper::showDate($created_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1, 2, 3])
            ->make();
    }

    public function editEntry($data, $id)
    {
        $fields = $data->except('_token', 'img', 'delete-img');

        if (strlen($fields['password']) == 0) {
            unset($fields['password']);
        } else {
            $fields['password'] = Hash::make($fields['password']);
        }

        if ($data->hasFile('img')) {
            $fields['img'] = UploadHelper::upload('img', $data, $this->folder, $id);
        }

        if ($data->has('delete-img')) {
            $fields['img'] = '';
        }

        if (strlen($fields['dob']) == 0) {
            $fields['dob'] = null;
        }

        $this->model->where('id', $id)->update($fields);

        return true;
    }

    public function createEntry($data)
    {
        $fields = $data->except('_token', 'img', 'delete-img');
        $fields['user_type'] = 3;

        if (strlen($fields['password']) == 0) {
            $password_plain = str_random(40);
            $fields['password'] = Hash::make($password_plain);
        } else {
            $password_plain = $fields['password'];
            $fields['password'] = Hash::make($password_plain);
        }

        if (strlen($fields['dob']) == 0) {
            $fields['dob'] = null;
        }

        $new = $this->model->create($fields);

        if ($data->hasFile('img')) {
            $new->img = UploadHelper::upload('img', $data, $this->folder, $new->id);
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
        UploadHelper::deleteDirectory($this->folder, $id);

        return $this->model->destroy($id);
    }

    public function export($data)
    {
        if ($data->has('start')) {
            $start = $data->get('start');
            $data = $this->model->members()->with('country')->where('created_at', '>=', $start . ' 00:00:00')->get();
        } else {
            $data = $this->model->members()->with('country')->get();
        }

        return $this->excel->create('members', function ($excel) use ($data) {
            $excel->sheet('Members', function ($sheet) use ($data) {
                $sheet->row(1, [
                    'ID', 'First Name', 'Last Name', 'Email', 'Mobile', 'DOB', 'Interests', 'Country', 'Timezone', 'IP', 'Registration Date', 'Last Login', 'Status', 'Activation Link'
                ]);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                });

                foreach ($data as $k => $v) {
                    $interests = $v->interests()->lists('title')->all();
                    $sheet->row(($k + 2), [
                        $v->id, $v->name, $v->surname, $v->email, $v->mobile, $v->dob, implode(', ', $interests), $v->country->title, $v->timezone, $v->ip, DateHelper::showDate($v->created_at), DateHelper::showDate($v->login_at), ($v->status == 1 ? 'Active' : 'Inactive'), (strlen($v->activation_token) > 0 ? config('app.url') . '/activate/' . $v->activation_token : '')
                    ]);
                }
            });
        })->download('xlsx');
    }

    public function import($data)
    {
        $import_options = [
            'date_format' => $data->get('date_format'),
            'timezone' => $data->get('timezone'),
            'country' => $data->get('country'),
            'pre_authenticated' => $data->get('pre_authenticated')
        ];
        $new = $this->import->create(['contents' => '', 'import_options' => serialize($import_options), 'user_id' => Auth::id()]);

        if ($data->hasFile('csv')) {
            $new->contents = UploadHelper::getFileContents('csv', $data);
            $new->save();
        }

        return redirect()->route('members.import.result', $new->id);
    }

    public function importFinalise($data)
    {
        $results = $this->parseMemberCSV($data->get('import_id'));

        $num = count($results['members']);

        if ($num > 0 && !$results['error']) {
            foreach ($results['members'] as $k => $v) {
                // Add class
                $dob = Carbon::parse(DateHelper::excel($v[5], '', $results['dateformat']))->format('Y-m-d');
                
                $new = $this->model->create([
                    'name' => $v[0], 
                    'surname' => $v[1], 
                    'dob' => $dob,
                    'mobile' => $v[4],
                    'img' => '',
                    'email' => $v[2], 
                    'password' => strlen($v[3]) > 0 ? Hash::make($v[3]) : Hash::make(str_random(20)),
                    'timezone' => $results['timezone'], 
                    'country_id' => $results['country'],
                    'pre_authenticated' => $results['pre_authenticated'],
                    'status' => 1, 
                    'login_at' => null,
                    'user_type' => 3,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        return redirect()->action('Admin\MemberController@showIndex')->with('flash_message_success', 'Import of ' . ($num == 1 ? '1 member' : $num . ' members') . ' complete.');
    }

    public function parseMemberCSV($id)
    {
        $ret = [
            'id' => $id,
            'error' => false,
            'message' => '<p>The uploaded file contained the members below. If you\'re happy with this, please click the CONFIRM IMPORT button to finalise the member import.</p>',
            'members' => []
        ];

        $csv_rec = $this->import->findOrFail($id);
        $csv = $csv_rec->contents;

        $options = unserialize($csv_rec->import_options);
        $ret['dateformat'] = $options['date_format'];
        $ret['timezone'] = $options['timezone'];
        $ret['country'] = $options['country'];
        $ret['pre_authenticated'] = $options['pre_authenticated'];

        $data = array_map('str_getcsv', preg_split('/\r*\n+|\r+/', $csv));

        foreach ($data as $k => $v) {
            // Is empty line
            if (count($v) != 6) {
                unset($data[$k]);
            }
            // Is header
            if (isset($v[0]) && $v[0] == 'First Name') {
                unset($data[$k]);
            }
            // Is Empty Data
            if (isset($v[0]) && $v[0] == '') {
                unset($data[$k]);
            }
        }

        $ret['members'] = $data;

        return $ret;
    }

    public function loginAs($id)
    {
        Auth::loginUsingId($id);

        return redirect('/');
    }
}
