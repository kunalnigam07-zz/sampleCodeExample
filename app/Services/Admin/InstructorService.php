<?php

namespace App\Services\Admin;

use Auth;
use Hash;
use Carbon;
use DateHelper;
use ClassHelper;
use UploadHelper;
use App\Models\User;
use App\Models\Setting;
use App\Models\ClassEvent;
use Maatwebsite\Excel\Excel;
use Yajra\Datatables\Datatables;

class InstructorService extends AdminService
{
    protected $folder = 'instructors';
    protected $columns = [
        'cols' => ['id', 'name', 'surname', 'email', 'created_at', 'status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(User $model, Excel $excel, ClassEvent $classevent, Setting $setting)
    {
        $this->model = $model;
        $this->excel = $excel;
        $this->classevent = $classevent;
        $this->setting = $setting;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\InstructorController@create')], ['Export', 'file-excel-o', action('Admin\InstructorController@showExport')], ['Earnings', 'gbp', action('Admin\InstructorController@showEarnings')]],
            'ajax' => route('instructors.dt.list', [], false),
            'labels' => ['ID', 'First Name', 'Last Name', 'Email', 'Registration Date', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = $this->model->instructors()->select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\InstructorController@loginAs\', $id) }}" class="login"><i class="fa fa-lock"></i></a>
                <a href="{{ action(\'Admin\InstructorController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\InstructorController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
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
        $fields = $data->except('_token', 'img', 'delete-img', 'signature_img', 'delete-signature_img');

        if (strlen($fields['password']) == 0) {
            unset($fields['password']);
        } else {
            $fields['password'] = Hash::make($fields['password']);
        }

        if ($data->hasFile('img')) {
            $fields['img'] = UploadHelper::upload('img', $data, $this->folder, $id);
        }

        if ($data->hasFile('signature_img')) {
            $fields['signature_img'] = UploadHelper::upload('signature_img', $data, $this->folder, $id);
        }

        if ($data->has('delete-img')) {
            $fields['img'] = '';
        }

        if ($data->has('delete-signature_img')) {
            $fields['signature_img'] = '';
        }

        if (strlen($fields['dob']) == 0) {
            $fields['dob'] = null;
        }

        $this->model->where('id', $id)->update($fields);

        return true;
    }

    public function createEntry($data)
    {
        $fields = $data->except('_token', 'img', 'delete-img', 'signature_img', 'delete-signature_img');
        $fields['user_type'] = 2;

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

        if ($data->hasFile('signature_img')) {
            $new->signature_img = UploadHelper::upload('signature_img', $data, $this->folder, $new->id);
            $new->save();
        }

        if ($data->has('delete-img')) {
            $new->img = '';
            $new->save();
        }

        if ($data->has('delete-signature_img')) {
            $new->signature_img = '';
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
            $data = $this->model->instructors()->with('country')->where('created_at', '>=', $start . ' 00:00:00')->get();
        } else {
            $data = $this->model->instructors()->with('country')->get();
        }

        return $this->excel->create('instructors', function ($excel) use ($data) {
            $excel->sheet('Instructors', function ($sheet) use ($data) {
                $sheet->row(1, [
                    'ID', 'First Name', 'Last Name', 'Email', 'Mobile', 'DOB', 'Interests', 'Country', 'Timezone', 'IP', 'Registration Date', 'Last Login', 'Pre-Authenticated', 'Status', 'Activation Link'
                ]);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                });

                foreach ($data as $k => $v) {
                    $interests = $v->interests()->lists('title')->all();
                    $sheet->row(($k + 2), [
                        $v->id, $v->name, $v->surname, $v->email,$v->mobile, $v->dob, implode(', ', $interests), $v->country->title, $v->timezone, $v->ip, DateHelper::showDate($v->created_at), DateHelper::showDate($v->login_at), ($v->pre_authenticated == 1 ? 'Yes' : 'No'), ($v->status == 1 ? 'Active' : 'Inactive'), (strlen($v->activation_token) > 0 ? config('app.url') . '/activate/' . $v->activation_token : '')
                    ]);
                }
            });
        })->download('xlsx');
    }

    public function earnings($data)
    {
        $month = $data->get('month');
        $data = [];

        $setting = $this->setting->findOrFail(1);
        $total_margin_percentage = $setting->margin_percentage;

        // Get all instructors
        $instructors = $this->model->instructors()->get();
        foreach ($instructors as $instructor) {
            $data[$instructor->id] = [
                'id' => $instructor->id,
                'name' => $instructor->name,
                'surname' => $instructor->surname,
                'email' => $instructor->email,
                'bank_name' => $instructor->bank_name,
                'bank_account_holder' => $instructor->bank_account_holder,
                'bank_account_number' => $instructor->bank_account_number,
                'bank_sort_code' => $instructor->bank_sort_code,
                'paypal_email' => $instructor->paypal_email,
                'classes_total' => 0,
                'attendances_total' => 0,
                'sub_total' => 0,
                'margin_total' => 0,
                'total' => 0
            ];
        }
        
        // Get all classes HELD in the selected month to update totals
        $classes = $this->classevent->where('class_at', 'LIKE', $month . '-%')->where('status', 1)->whereNull('cancelled_at')->where('total_gross', '>', 0)->orderBy('class_at', 'ASC')->get();
        foreach ($classes as $class) {
            $c_total_gross = $class->total_gross;
            $c_total_margin_percentage = $class->total_margin_percentage;
            $c_total_attended = $class->total_attended;
            $c_instructor_id = $class->user_id;

            $margin_amount = $c_total_gross / 100 * $c_total_margin_percentage;
            $earnings_amount = $c_total_gross - $margin_amount;

            $data[$c_instructor_id]['classes_total']++;
            $data[$c_instructor_id]['attendances_total'] += $c_total_attended;
            $data[$c_instructor_id]['sub_total'] += $c_total_gross;
            $data[$c_instructor_id]['margin_total'] += round($margin_amount, 2);
            $data[$c_instructor_id]['total'] += round($earnings_amount, 2);
        }

        // Output to Excel
        return $this->excel->create('earnings', function ($excel) use ($data, $instructors, $month, $total_margin_percentage, $classes) {
            $excel->sheet('Earnings', function ($sheet) use ($data) {
                $sheet->row(1, [
                    'ID', 'First Name', 'Last Name', 'Email', 'Total Classes', 'Attendances', 'Sub-Total', 'Margin Total', 'Total', 'PayPal Email', 'Bank Name', 'Account Holder', 'Account Number', 'Sort Code'
                ]);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                });

                $counter = 1;
                foreach ($data as $k => $v) {
                    if ($v['total'] > 0) {
                        $counter++;
                        $sheet->row($counter, [
                            $v['id'], $v['name'], $v['surname'], $v['email'], $v['classes_total'], $v['attendances_total'], $v['sub_total'], $v['margin_total'], $v['total'], $v['paypal_email'], $v['bank_name'], $v['bank_account_holder'], $v['bank_account_number'], $v['bank_sort_code']
                        ]);

                        $sheet->setColumnFormat(['B' . $counter => '@']);
                        $sheet->setColumnFormat(['C' . $counter => '@']);
                        $sheet->setColumnFormat(['D' . $counter => '@']);
                        $sheet->setColumnFormat(['J' . $counter => '@']);
                        $sheet->setColumnFormat(['K' . $counter => '@']);
                        $sheet->setColumnFormat(['L' . $counter => '@']);
                        $sheet->setColumnFormat(['M' . $counter => '@']);
                        $sheet->setColumnFormat(['N' . $counter => '@']);

                        $sheet->setColumnFormat(['G' . $counter => '[$£]#,##0.00_-']);
                        $sheet->setColumnFormat(['H' . $counter => '[$£]#,##0.00_-']);
                        $sheet->setColumnFormat(['I' . $counter => '[$£]#,##0.00_-']);
                    }
                }
            });

            // Each instructor...
            foreach ($instructors as $instructor) {
                if ($data[$instructor->id]['total'] > 0) {
                    $excel->sheet($instructor->name . ' ' . $instructor->surname, function ($sheet) use ($instructor, $month, $data, $total_margin_percentage, $classes) {
                        $sheet->setAutoSize(false);
                        $sheet->setWidth(['A' => 5, 'B' => 15, 'C' => 15, 'D' => 15, 'E' => 15, 'F' => 15, 'G' => 15, 'H' => 15, 'I' => 15, 'J' => 15, 'K' => 15, 'L' => 15, 'm' => 15]);
                        // --------------------------------------------------------------------------------------------------------------------------------
                        // Instructor Name
                        // --------------------------------------------------------------------------------------------------------------------------------
                        $sheet->cell('B3', function($cell) {
                            $cell->setValue('Instructor Name');
                        });
                        $sheet->cell('C3', function($cell) use ($instructor) {
                            $cell->setValue($instructor->name . ' ' . $instructor->surname);
                            $cell->setFontWeight('bold');
                        });
                        // --------------------------------------------------------------------------------------------------------------------------------
                        // Instructor Email
                        // --------------------------------------------------------------------------------------------------------------------------------
                        $sheet->cell('B4', function($cell) {
                            $cell->setValue('Instructor Email');
                        });
                        $sheet->cell('C4', function($cell) use ($instructor) {
                            $cell->setValue($instructor->email);
                            $cell->setFontWeight('bold');
                        });
                        // --------------------------------------------------------------------------------------------------------------------------------
                        // Month
                        // --------------------------------------------------------------------------------------------------------------------------------
                        $sheet->cell('F3', function($cell) {
                            $cell->setValue('Month Ending');
                        });
                        $sheet->cell('G3', function($cell) use ($month) {
                            $cell->setValue(Carbon::parse($month . '-01')->format('M Y'));
                        });
                        // --------------------------------------------------------------------------------------------------------------------------------
                        // Summary - Labels
                        // --------------------------------------------------------------------------------------------------------------------------------
                        $sheet->cell('B7', function($cell) {
                            $cell->setValue('Summary');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('B8', function($cell) {
                            $cell->setValue('Number of Classes Held');
                        });
                        $sheet->cell('B9', function($cell) {
                            $cell->setValue('Number of Members Taught');
                        });
                        $sheet->cell('B11', function($cell) {
                            $cell->setValue('Payment will be effected within the first week of the following month with payment hitting your account several days after.');
                        });
                        $sheet->cell('B13', function($cell) {
                            $cell->setValue('Classes');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('F8', function($cell) {
                            $cell->setValue('Margin');
                        });
                        $sheet->cell('F9', function($cell) {
                            $cell->setValue('Total Income (Minus Margin)');
                            $cell->setFontWeight('bold');
                        });
                        // --------------------------------------------------------------------------------------------------------------------------------
                        // Summary - Values
                        // --------------------------------------------------------------------------------------------------------------------------------
                        $sheet->cell('D8', function($cell) use ($data, $instructor) {
                            $cell->setValue($data[$instructor->id]['classes_total']);
                        });
                        $sheet->cell('D9', function($cell) use ($data, $instructor) {
                            $cell->setValue($data[$instructor->id]['attendances_total']);
                        });
                        $sheet->cell('H9', function($cell) use ($data, $instructor) {
                            $cell->setValue($data[$instructor->id]['total']);
                        });
                        $sheet->setColumnFormat(['H9' => '[$£]#,##0.00_-']);
                        $sheet->cell('H8', function($cell) use ($total_margin_percentage) {
                            $cell->setValue($total_margin_percentage / 100);
                        });
                        $sheet->setColumnFormat(['H8' => '0%']);
                        // --------------------------------------------------------------------------------------------------------------------------------
                        // Class list headings
                        // --------------------------------------------------------------------------------------------------------------------------------
                        $sheet->row(15, [
                            '', 'Date', 'Time', 'Duration', 'Class Type', 'Class Title', '', 'Member', 'Fee Type', 'Fee Paid', 'Commission', 'Your Revenue'
                        ]);
                        $sheet->cells('B15:L15', function($cells) {
                            $cells->setBorder(['bottom' => ['style' => 'thin']]);
                            $cells->setAlignment('center');
                        });
                        // --------------------------------------------------------------------------------------------------------------------------------
                        // Actual listing
                        // --------------------------------------------------------------------------------------------------------------------------------
                        $row_ref = 16; // Start adding lines on this row
                        $tot_fee = 0;
                        $tot_commission = 0;
                        $tot_your = 0;

                        foreach ($classes as $class) {
                            if ($class->user_id == $instructor->id) {
                                // Class details
                                $class_tags = ClassHelper::tags($class->type_1_id . '_' . $class->type_2_id . '_' . $class->type_3_id);
                                $sheet->row($row_ref, [
                                    '', DateHelper::showDate($class->class_at, 'j M Y'), DateHelper::showDate($class->class_at, 'H:i'), DateHelper::duration($class->class_at, $class->class_ends_at) . ' mins', $class_tags[1], $class->title
                                ]);
                                // Bookings details

                                $summaries = [];
                                if (strlen($class->class_data) > 0) {
                                    $class_data = unserialize($class->class_data);

                                    $sub_tot_fee = 0;
                                    $sub_tot_commission = 0;
                                    $sub_tot_your = 0;

                                    foreach ($class_data as $kk => $vv) {
                                        $row_ref++;
                                        if ($class->total_margin_percentage > 0) {
                                            $commission = round($vv['fee_total'] / 100 * $class->total_margin_percentage, 2);
                                        } else {
                                            $commission = 0;
                                        }

                                        $your_revenue = round($vv['fee_total'] - $commission, 2);

                                        $tot_fee += $vv['fee_total'];
                                        $tot_commission += $commission;
                                        $tot_your += $your_revenue;

                                        $sub_tot_fee += $vv['fee_total'];
                                        $sub_tot_commission += $commission;
                                        $sub_tot_your += $your_revenue;

                                        $sheet->row($row_ref, [
                                            '', '', '', '', '', '', '', $vv['member_name'] . ' ' . $vv['member_surname'][0] . '.', ClassHelper::feeTypeFriendly($vv['fee_type']), $vv['fee_total'], $commission, $your_revenue
                                        ]);

                                        $sheet->setColumnFormat(['J' . $row_ref => '[$£]#,##0.00_-']);
                                        $sheet->setColumnFormat(['K' . $row_ref => '[$£]#,##0.00_-']);
                                        $sheet->setColumnFormat(['L' . $row_ref => '[$£]#,##0.00_-']);
                                    }

                                    $row_ref++;

                                    $sheet->cell('G' . $row_ref, function($cell) {
                                        $cell->setValue('Total');
                                    });

                                    $sheet->cell('J' . $row_ref, function($cell) use ($sub_tot_fee) {
                                        $cell->setValue($sub_tot_fee);
                                    });
                                    $sheet->cell('K' . $row_ref, function($cell) use ($sub_tot_commission) {
                                        $cell->setValue($sub_tot_commission);
                                    });
                                    $sheet->cell('L' . $row_ref, function($cell) use ($sub_tot_your) {
                                        $cell->setValue($sub_tot_your);
                                    });

                                    $sheet->setColumnFormat(['J' . $row_ref => '[$£]#,##0.00_-']);
                                    $sheet->setColumnFormat(['K' . $row_ref => '[$£]#,##0.00_-']);
                                    $sheet->setColumnFormat(['L' . $row_ref => '[$£]#,##0.00_-']);

                                    $sheet->cells('J' . $row_ref . ':L' . $row_ref, function($cells) {
                                        $cells->setBorder(['top' => ['style' => 'thin']]);
                                    });
                                }

                                // Increment
                                $row_ref += 2;
                            }
                        }

                        // --------------------------------------------------------------------------------------------------------------------------------
                        // Bottom totals
                        // --------------------------------------------------------------------------------------------------------------------------------
                        $row_ref++;

                        $sheet->row($row_ref, [
                            '', '', '', '', '', '', 'Classes', 'Members', '', 'Fee Paid', 'Commission', 'Your Revenue'
                        ]);
                        $sheet->cells('G' . $row_ref . ':H' . $row_ref, function($cells) {
                            $cells->setBorder(['bottom' => ['style' => 'thin']]);
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('J' . $row_ref . ':L' . $row_ref, function($cells) {
                            $cells->setBorder(['bottom' => ['style' => 'thin']]);
                            $cells->setAlignment('center');
                        });

                        $row_ref++;

                        $sheet->cell('F' . $row_ref, function($cell) {
                            $cell->setValue('Grand Total');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cells('G' . $row_ref . ':H' . $row_ref, function($cells) {
                            $cells->setBorder(['bottom' => ['style' => 'double']]);
                            $cells->setAlignment('center');
                            $cells->setFontWeight('bold');
                        });
                        $sheet->cells('J' . $row_ref . ':L' . $row_ref, function($cells) {
                            $cells->setBorder(['bottom' => ['style' => 'double']]);
                            $cells->setAlignment('center');
                            $cells->setFontWeight('bold');
                        });

                        // Values
                        $sheet->cell('G' . $row_ref, function($cell) use ($data, $instructor) {
                            $cell->setValue($data[$instructor->id]['classes_total']);
                        });
                        $sheet->cell('H' . $row_ref, function($cell) use ($data, $instructor) {
                            $cell->setValue($data[$instructor->id]['attendances_total']);
                        });

                        $sheet->cell('J' . $row_ref, function($cell) use ($tot_fee) {
                            $cell->setValue($tot_fee);
                        });
                        $sheet->cell('K' . $row_ref, function($cell) use ($tot_commission) {
                            $cell->setValue($tot_commission);
                        });
                        $sheet->cell('L' . $row_ref, function($cell) use ($tot_your) {
                            $cell->setValue($tot_your);
                        });

                        $sheet->setColumnFormat(['J' . $row_ref => '[$£]#,##0.00_-']);
                        $sheet->setColumnFormat(['K' . $row_ref => '[$£]#,##0.00_-']);
                        $sheet->setColumnFormat(['L' . $row_ref => '[$£]#,##0.00_-']);
                    });
                }
                
            }
        })->download('xlsx');
    }

    public function loginAs($id)
    {
        Auth::loginUsingId($id);

        return redirect('/');
    }
}
