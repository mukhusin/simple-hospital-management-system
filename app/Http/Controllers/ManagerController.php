<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\AttendanceBill;
use App\Models\AttendanceCheckout;
use App\Models\AttendanceClinical;
use App\Models\AttendanceLab;
use App\Models\AttendanceMedical;
use App\Models\AttendanceMovement;
use App\Models\AttendancePayment;
use App\Models\Diagnosis;
use App\Models\Expense;
use App\Models\ExpenseTransaction;
use App\Models\InSession;
use App\Models\Insurance;
use App\Models\InsurancePrice;
use App\Models\LabTest;
use App\Models\Logs;
use App\Models\Medical;
use App\Models\MedicalStock;
use App\Models\Office;
use App\Models\Patient;
use App\Models\PatientAttendance;
use App\Models\PatientVital;
use App\Models\PaymentBreak;
use App\Models\Procedure;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Ward;
use App\Models\WardCheck;
use App\Models\Widget;

class ManagerController extends Controller
{


    function __construct() {}

    public function lab_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'lab/get'
        );
        $data = array(
            'title' => 'Patient',
            'text_string' => '<a class="btn btn-primary" href="' . url('lab/edit/0') . '">Add New</a> <hr>',
            'table' => (object)array(
                'columns' => array(
                    'No',
                    'Code',
                    'UOM',
                    'Diagnosis',
                    'Price',
                    'Option',
                )
            )
        );
        $_title = 'Lab Test';
        $_sub_title = '';
        $_page_index = 'lab';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function lab_index_get()
    {
        $l = DB::table('lab_tests');

        $l->select('id', 'code', 'uom', 'name', 'price');
        // $l->where('creator_id','=',Auth::user()->id);
        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('lab/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
            // ->removeColumn('id')->toJson();
            ->toJson();
    }


    public function bill_edit($id)
    {
        $data = array(
            'status' => 'New',
        );
        $sub_title = '<i class="icon-plus"></i>';
        if ($id == 0) {
            $title = "Create New Bill";
            $page_index = '';
        } else {
            $l = AttendanceBill::find($id);
            $title = "Edit Attendance Bill";
            $page_index = '';

            $data = array(
                'bill' => $l,
                'status' => 'Edit'
            );
        }

        $_title = $title;
        $_sub_title = $sub_title;
        $_page_index = $page_index;
        return view('manager.bill', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function bill_edit_save($id)
    {
        if ($id == 0) {
            $l = new AttendanceBill();
        } else {
            $l = AttendanceBill::find($id);
        }
        $l->dosage = request('dosage');
        $l->name = request('name');
        $l->amount = request('amount');
        $l->save();

        if ($l->name == 'Consultation Fee') {
            $_a = $l->attendance;
            $_a->consultation_fee = $l->amount;
            $_a->save();
        }

        return Redirect::to('dashboard')->with('success', 'Record Saved');
    }

    public function lab_edit($id)
    {
        $data = array(
            'status' => 'New',
        );
        $sub_title = '<i class="icon-plus"></i>';
        if ($id == 0) {
            $title = "Create New Lab Test";
            $page_index = 'lab';
        } else {
            $l = LabTest::find($id);
            $title = "Edit Lab Test";
            $page_index = 'lab';

            $data = array(
                'lab' => $l,
                'status' => 'Edit'
            );
        }

        $_title = $title;
        $_sub_title = $sub_title;
        $_page_index = $page_index;
        return view('manager.lab', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function lab_edit_save($id)
    {
        // return request()->all();
        if ($id == 0) {
            $l = new LabTest();
        } else {
            $l = LabTest::find($id);
        }
        $l->code = request('code');
        $l->uom = request('uom');
        $l->name = request('name');
        $l->price = request('price');
        $l->save();


        if ($id > 0) {
            $i_id = request('insurance_id');
            $i = -1;
            foreach ($i_id as $key => $value) {
                ++$i;
                $ip = InsurancePrice::find(request('insurance_id')[$i]);
                $ip->price = request('insurance_price')[$i];
                $ip->save();
            }
        }

        return Redirect::to('lab')->with('success', 'Record Saved');
    }


    public function med_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'medicines/get'
        );
        $data = array(
            'title' => 'Patient',
            'text_string' => '<a class="btn btn-primary" href="' . url('medicines/edit/0') . '">Add New</a> <hr>',
            'table' => (object)array(
                'columns' => array(
                    'Name',
                    'Brand',
                    'Unit',
                    'Price Unit',
                    'Stock',
                    'Option',
                )
            )
        );
        $_title = 'Medicines';
        $_sub_title = '';
        $_page_index = 'medicine';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function med_index_get()
    {
        $l = DB::table('medicals');

        $l->select('id', 'name', 'brand', 'unit', 'price_unit', 'stock');
        // $l->where('creator_id','=',Auth::user()->id);
        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('medicines/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';
            $opt .= '<a class="opt" href="' . url('medicines/view/' . $row->id) . '"><i class="icon-eye-open"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
            // ->toJson();
            ->removeColumn('id')->toJson();
    }

    public function med_edit($id)
    {
        $data = array(
            'status' => 'New',
        );
        $sub_title = '<i class="icon-plus"></i>';
        if ($id == 0) {
            $title = "Create New Medicine";
            $page_index = 'medicine';
        } else {
            $m = Medical::find($id);
            $title = "Edit Medicine";
            $page_index = 'medicine';

            $data = array(
                'medical' => $m,
                'status' => 'Edit'
            );
        }

        $_title = $title;
        $_sub_title = $sub_title;
        $_page_index = $page_index;
        return view('manager.medical', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function med_edit_save($id)
    {
        $s = request('stock', 0);
        if ($id == 0) {
            $l = new Medical();
        } else {
            $l = Medical::find($id);
            $s = request('stock', 0) - $l->stock;
        }
        $l->name = request('name');
        $l->brand = request('brand');
        $l->unit = request('unit');
        $l->stock = request('stock', 0);
        $l->price_unit = request('price_unit');
        $l->save();


        if ($id > 0) {
            $i_id = request('insurance_id');
            $i = -1;
            foreach ($i_id as $key => $value) {
                ++$i;
                $ip = InsurancePrice::find(request('insurance_id')[$i]);
                $ip->price = request('insurance_price')[$i];
                $ip->save();
            }
        }

        $ms = new MedicalStock();
        $ms->medical_id = $l->id;
        $ms->changes = $s;
        $ms->stock = $l->stock;
        $ms->remark = 'Manual Changes';
        $ms->creator_id = Auth::user()->id;
        $ms->save();

        return Redirect::to('medicines')->with('success', 'Record Saved');
    }

    public function med_view($id)
    {

        $m = Medical::find($id);
        $sub_title = '<i class="icon-plus"></i>';
        if ($m) {

            $title = "View Medicine";
            $page_index = 'medicine';

            $data = array(
                'medical' => $m,
                'status' => 'View',
                'source' => url('medicines/stock/get/'.$m->id)
            );

            $_title = $title;
            $_sub_title = $sub_title;
            $_page_index = $page_index;
            return view('manager.medical2', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

        }
    }

    public function med_view_get($id)
    {
        $l = DB::table('medical_stocks_data');

        $l->select('id', 'created_at', 'stock', 'stock_before', 'changes', 'remark', 'creator');
        $l->where('medical_id', '=', $id);

        return DataTables::of($l)
            ->editColumn('created_at', function($row) {
            return date('H:i d M Y',strtotime($row->created_at));
        })
            ->removeColumn('id')->toJson();
    }

    public function dia_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'diagnosis/get'
        );
        $data = array(
            'title' => 'Patient',
            'text_string' => '<a class="btn btn-primary" href="' . url('diagnosis/edit/0') . '">Add New</a> <hr>',
            'table' => (object)array(
                'columns' => array(
                    'No',
                    'Code',
                    'Disease',
                    'Option',
                )
            )
        );
        $_title = 'Diagnosis List';
        $_sub_title = '';
        $_page_index = 'diagnosis';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function dia_index_get()
    {
        $l = DB::table('diagnosis');

        $l->select('id', 'code', 'name');
        // $l->where('creator_id','=',Auth::user()->id);
        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('diagnosis/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
            ->toJson();
        // ->removeColumn('id')->toJson();
    }

    public function dia_edit($id)
    {
        $data = array(
            'status' => 'New',
        );
        $sub_title = '<i class="icon-plus"></i>';
        if ($id == 0) {
            $title = "Create New Diagnosis";
            $page_index = 'diagnosis';
        } else {
            $m = Diagnosis::find($id);
            $title = "Edit Diagnosis";
            $page_index = 'diagnosis';

            $data = array(
                'diagnosis' => $m,
                'status' => 'Edit'
            );
        }

        $_title = $title;
        $_sub_title = $sub_title;
        $_page_index = $page_index;
        return view('manager.diagnosis', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

    }

    public function dia_edit_save($id)
    {
        if ($id == 0) {
            $l = new Diagnosis();
        } else {
            $l = Diagnosis::find($id);
        }

        $l->code = request('code');
        $l->name = request('name');
        $l->save();

        return Redirect::to('diagnosis')->with('success', 'Record Saved');
    }


    public function user_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'users/get'
        );
        $data = array(
            'title' => 'Patient',
            'text_string' => '<a class="btn btn-primary" href="' . url('user/edit/0') . '">Add New</a> <hr>',
            'table' => (object)array(
                'columns' => array(
                    'Full Name',
                    'username',
                    'Role',
                    'Office',
                    'Option',
                )
            )
        );
        $_title = 'Users';
        $_sub_title = '';
        $_page_index = 'user';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }


    public function user_index_get()
    {
        $l = DB::table('users_data');

        $l->select('id', 'name', 'username', 'role', 'office');
        $l->where('hide', '=', 0);
        $l->where('role_id', '<', 6);
        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('user/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
            ->removeColumn('id')->toJson();
        // ->toJson();
    }


    public function user_edit($id)
    {
        $data = array(
            'status' => 'New',
        );
        $sub_title = '<i class="icon-plus"></i>';
        if ($id == 0) {
            $title = "Create New User";
            $page_index = 'user';
        } else {
            $l = User::find($id);
            $title = "Edit User";
            $page_index = 'user';

            $data = array(
                'user' => $l,
                'status' => 'Edit'
            );
        }

        $_title = $title;
        $_sub_title = $sub_title;
        $_page_index = $page_index;
        return view('manager.user', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function user_edit_save($id)
    {
        if ($id == 0) {
            $l = new User();
        } else {
            $l = User::find($id);
        }
        // return request()->all();
        $l->name = request('name');
        $l->username = request('username');
        $l->password = Hash::make(request('password'));
        $l->username = request('username');
        $l->office_id = request('office_id');
        $l->role_id = request('role_id');
        $l->hide = 0;
        $l->level = 0;
        $l->save();

        return Redirect::to('users')->with('success', 'Record Saved');
    }


    public function user_disabled_toggle($id)
    {
        #
        $m = User::find($id);
        $toggle = array(1, 0);
        $m->hide = $toggle[$m->hide];
        $m->updator_id = Auth::user()->id;
        $m->save();

        return Redirect::to('users')->with('success', 'User Deleted');
        #
    }


    public function bills_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'widget/get/bills'
        );
        $data = array(
            'title' => 'Patient',
            'table' => (object)array(
                'columns' => array(
                    'Patient ID',
                    'Patient Full Name',
                    'Gender',
                    'Bill Description',
                    'Bill Amount',
                    'Option',
                )
            )
        );
        $_title = 'Patient Bills';
        $_sub_title = '';
        $_page_index = 'bills';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }


    public function inventory_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'report/inventory/get'
        );

        $_title = 'Medicine Inventory Summary';
        $_sub_title = '';
        $_page_index = 'report';
        return view('manager.inventory', ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]);
    }


    public function inventory_index_get()
    {
        $l = DB::table('medical_stocks_data');

        $l->select('id', 'created_at', 'medicine', 'brand', 'changes', 'stock', 'remark', 'creator', 'payment_id');

        return DataTables::of($l)
            ->editColumn('payment_id', function($row) {
                return $row->payment_id
                    ? '<a target="_new" href="' . url('receipt/view/' . $row->payment_id) . '">Receipt</a>'
                    : '';
            })
            ->rawColumns(['payment_id'])
            ->removeColumn('id')->toJson();
    }

    public function inventory_report1()
    {
        // return request()->all();
//        $date = request('date', null);

        #
        $from = request('from', date('Y-m-d', strtotime("last month")));
        $to = request('to', date('Y-m-d'));
        #

        $s = DB::table('medical_stocks_data')
            ->select('medical_id', 'medicine', 'brand', DB::raw('sum(changes) as changes, remark'))
//            ->where(DB::raw('date(created_at)'), '=', $date)
            ->whereRaw('date(created_at) >= ?', [$from])
            ->whereRaw('date(created_at) <= ?', [$to])
            ->groupBy('medical_id', 'remark');

        $c = DB::table('medical_stocks_data')
            ->whereRaw('date(created_at) >= ?', [$from])
            ->whereRaw('date(created_at) <= ?', [$to])
//            ->where(DB::raw('date(created_at)'), '=', $date)
            ->orderBy('created_at', 'desc');

        $meds = array();
        $med_key = $start = $end = array();
        foreach ($c->get() as $key => $value) {
            if (!in_array($value->medical_id, $med_key)) {
                $end[$value->medical_id] = $value->stock;
                $med_key[] = $value->medical_id;
                $meds[$value->medical_id]['name'] = $value->medicine;
                $meds[$value->medical_id]['brand'] = $value->brand;
                $meds[$value->medical_id]['purchased'] = 0;
                $meds[$value->medical_id]['sale'] = 0;
                $meds[$value->medical_id]['other'] = 0;
                $meds[$value->medical_id]['open'] = 0;
                $meds[$value->medical_id]['close'] = $value->stock;
            }
            $start[$value->medical_id] = $value->stock - $value->changes;
            $meds[$value->medical_id]['open'] = $value->stock - $value->changes;
        }

        foreach ($s->get() as $key => $value) {

            if (strtolower($value->remark) == 'sale') {
                $meds[$value->medical_id]['sale'] += $value->changes;
            } else if (strtolower($value->remark) == 'purchased') {
                $meds[$value->medical_id]['purchased'] += $value->changes;
            } else {
                $meds[$value->medical_id]['other'] += $value->changes;
            }

        }

        $data = array(
            'summary' => $s->get(),
            'changes' => $c->get(),
            'start' => $start,
            'end' => $end,
            'meds' => $meds,
            'med_key' => $med_key,
            'from' => $from,
            'to' => $to,
            'type' => request('type', 'view'),
        );

        if ($data['type'] == 'download') {
            return $this->download_inventory_report1($data);
        } else {
            return view('report.print_stock_report1', $data);
        }

    }

    public function download_inventory_report1($data)
    {

        $values = [];

        $history = DB::table('medical_history')
            ->whereRaw('date(created_at) >= ?', [$data['from']])
            ->whereRaw('date(created_at) <= ?', [$data['to']])
            ->where('checked', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($history as $med) {

            $temp = [];

            $temp['Date'] = date('H:i d M Y', strtotime($med->created_at));
            $temp['Medicine'] = $med->medicine . ' ' . $med->brand;
            $temp['Patient'] = $med->patient;
            $temp['Gender'] = $med->gender;
            $temp['Dosage'] = $med->dosage;
            $temp['Quantity'] = $med->quantity;

            $values[] = $temp;

        }

        $fileName_1 = 'Medicine_Inventory:from_' . $data["from"] . '_to_' . $data["to"] . '.csv';
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName_1}");
        header("Expires: 0");
        header("Pragma: public");
        $fh1 = @fopen('php://output', 'w');
        $headerDisplayed1 = false;
        foreach ($values as $data1) {
            // Add a header row if it hasn't been added yet
            if (!$headerDisplayed1) {
                // Use the keys from $data as the titles
                fputcsv($fh1, array_keys($data1));
                $headerDisplayed1 = true;
            }
            // Put the data into the stream
            fputcsv($fh1, $data1);
        }
        // Close the file
        fclose($fh1);
        // Make sure nothing else is sent, our file is done

        exit;

    }

    public function lab_report_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'report/lab/get'
        );

        $_title = 'Laboratory Report';
        $_sub_title = '';
        $_page_index = 'report';
        return view('manager.lab_report', ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]);
    }


    public function lab_report_index_get()
    {
        $l = DB::table('attendance_labs_data');

        $l->select('id', 'created_at', 'test', 'patient', 'gender', 'doctor', 'technician', 'results_register', 'attachment');

        return DataTables::of($l)
            ->editColumn('attachment', function($row) {
                return $row->attachment
                    ? '<a target="_new" href="' . url($row->attachment) . '">Link</a>'
                    : '-';
            })
            ->rawColumns(['attachment'])
            ->removeColumn('id')->toJson();

    }

    public function lab_report_report1()
    {
//         return request()->all();
        $date_from = request('from', null);
        $date_to = request('to', null);
        $s = DB::table('attendance_labs_data')
            ->select('test_id', 'test', 'doctor', DB::raw('count(test_id) as times'))
            ->where(DB::raw('date(created_at)'), '>=', $date_from)
            ->where(DB::raw('date(created_at)'), '<=', $date_to)
            ->groupBy('test_id', 'doctor');

        $c = DB::table('attendance_labs_data')
            ->where(DB::raw('date(created_at)'), '>=', $date_from)
            ->where(DB::raw('date(created_at)'), '<=', $date_to)
            ->orderBy('created_at', 'desc');

        $data = array(
            'summary' => $s->get(),
            'changes' => $c->get(),
            'from' => $date_from,
            'to' => $date_to,
        );

        if (request('type', 'view') == 'view') {
            return view('report.print_lab_report1', $data);
        } else {
            return $this->download_lab_report_report1($data);
        }

    }


    public function download_lab_report_report1($data)
    {

        $values = [];

        foreach ($data['changes'] as $test) {

            $temp = [];

            $temp['Date'] = date('H:i d M Y', strtotime($test->created_at));
            $temp['Test'] = $test->test;
            $temp['Patient'] = $test->patient;
            $temp['Gender'] = $test->gender;
            $temp['Doctor'] = $test->doctor;
            $temp['Technician'] = $test->technician;
            $temp['Results'] = $test->results_register;

            $values[] = $temp;

        }

        $fileName_1 = 'Laboratory:from_' . $data["from"] . '_to_' . $data["to"] . '.csv';
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName_1}");
        header("Expires: 0");
        header("Pragma: public");
        $fh1 = @fopen('php://output', 'w');
        $headerDisplayed1 = false;
        foreach ($values as $data1) {
            // Add a header row if it hasn't been added yet
            if (!$headerDisplayed1) {
                // Use the keys from $data as the titles
                fputcsv($fh1, array_keys($data1));
                $headerDisplayed1 = true;
            }
            // Put the data into the stream
            fputcsv($fh1, $data1);
        }
        // Close the file
        fclose($fh1);
        // Make sure nothing else is sent, our file is done

        exit;

    }

    public function followup_index()
    {
        $_sub_title = '';
        $_page_index = 'followup';

        $_title = 'Patient Followup Callendar';
        return view('followup', ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]);
    }


    public function followup_index_get()
    {

        $start = request('start');
        $end = request('end');

        $a = new PatientAttendance();
        $a = $a->where('followup', '!=', 'NULL');
        $a = $a->where('followup', '!=', '');

        $a = $a->where('followup', '>=', $start);
        $a = $a->where('followup', '<=', $end);
        $a = $a->get();

        $data = array();

        foreach ($a as $key => $value) {

            $p = $value->patient;

            $date = date('Y-m-d', strtotime($value->followup));
            $time = date('H:i:s', strtotime($value->followup_time));

            $temp_start = $date . 'T' . $time;

            $temp_end = strtotime($value->followup_time) + 60 * 60 * 1;
            $temp_end = date('H:i:s', $temp_end);

            $temp_end = $date . 'T' . $temp_end;

            $temp = array(
                'id' => $value->id,
                'title' => $p->name . ' - ' . $p->gender,
                'start' => $temp_start,
                'end' => $temp_end,
            );

            $data[] = $temp;
        }

        return $data;

    }


    public function sales_report1()
    {

        $from = request('from', date('Y-m-d', strtotime("last month")));
        $to = request('to', date('Y-m-d'));

        $data = DB::table('paid_bills')
            ->select(DB::raw('date(created_at) as dat'), DB::raw('sum(amount) as sum'), 'group')
            // ->where('reattend','=',0)
            ->whereRaw('date(created_at) >= ?', [$from])
            ->whereRaw('date(created_at) <= ?', [$to])
            ->groupBy(DB::raw('date(created_at)'), 'group')
            ->orderby('created_at', 'desc')
//				->limit(60)
            ->get();

//        dd($data);

        $dat = [];
        #zero loop
        foreach ($data as $key => $value) {
            $dat[$value->dat]['Laboratory'] = 0;
            $dat[$value->dat]['Pharmacy'] = 0;
            $dat[$value->dat]['Service Charge'] = 0;
            $dat[$value->dat]['Consultation Fee'] = 0;
            $dat[$value->dat]['Total Bill'] = 0;
            $dat[$value->dat]['Amount Paid'] = 0;
        }
        #Value loop
        foreach ($data as $key => $value) {
            $dat[$value->dat][$value->group] = $value->sum;
        }

        foreach ($dat as $key => $value) {
            $va = DB::table('attendance_payments')
                ->select(DB::raw('date(created_at) as dat'), DB::raw('sum(paid) as sum'))
                ->groupBy(DB::raw('date(created_at)'))
                ->where('mode', '=', 'Cash')
                ->where(DB::raw('date(created_at)'), '=', $key)
                ->get();

            $va2 = DB::table('attendance_payments')
                // ->select('created_at', 'amount','group')
                ->select(DB::raw('date(created_at) as dat'), DB::raw('sum(paid) as sum'))
                ->where('mode', '!=', 'Cash')
                ->where(DB::raw('date(created_at)'), '=', $key)
                ->groupBy(DB::raw('date(created_at)'))
//                 ->groupBy(DB::raw('date(created_at)'),'group')
                ->orderby('created_at', 'desc')
                ->get();
//            return $va2;
            $dat[$key]['Amount Paid'] = (isset($va[0])) ? $va[0]->sum : 0;
            $dat[$key]['Cash'] = (isset($va[0])) ? $va[0]->sum : 0;
            $dat[$key]['Credit'] = (isset($va2[0])) ? $va2[0]->sum : 0;
            $dat[$key]['Total Bill'] = $value['Laboratory'] + $value['Pharmacy'] + $value['Service Charge'] + $value['Consultation Fee'];

        }

        $data = array(
            'data' => $dat,
            'from' => $from,
            'to' => $to,
        );
//		 return $data;
        $_title = 'Report';
        $_sub_title = 'Sales';
        $_page_index = 'report';
        return view('manager.sales', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

        // return $dat;
    }

    public function sales_report2($date)
    {
        $data2 = DB::table('paid_bills')
            // ->select('created_at', 'amount','group')
            ->where(DB::raw('date(created_at)'), '=', $date)
            // ->groupBy(DB::raw('date(created_at)'),'group')
            ->orderby('created_at', 'desc')
            ->get();

        $data1 = DB::table('attendance_payment_data')
            ->where(DB::raw('date(created_at)'), '=', $date)
            ->orderby('created_at', 'desc')
            ->get();

        // return $data1;

        $data = array(
            'data1' => $data1,
            'data2' => $data2,
        );
//		 return $data;
        $_title = ' Report - ' . date('D, d M Y', strtotime($date));
        $_sub_title = 'Income';
        $_page_index = 'report';
        return view('manager.sales2', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

        // return $dat;

    }


    public function income_statement()
    {

        $from = request('from', date('Y-m-d', strtotime("last year")));
        $to = request('to', date('Y-m-d'));

        $data = DB::table('paid_bills')
            ->select(DB::raw('date(created_at) as dat'), DB::raw('sum(amount) as sum'), 'group')
            // ->where('reattend','=',0)
            ->whereRaw('date(created_at) >= ?', [$from])
            ->whereRaw('date(created_at) <= ?', [$to])
            ->groupBy(DB::raw('date(created_at)'), 'group')
            ->orderby('created_at', 'desc')
//            ->limit(60)
            ->get();

        $dat = [];
        #zero loop
        foreach ($data as $key => $value) {
            $dat[$value->dat]['Laboratory'] = 0;
            $dat[$value->dat]['Pharmacy'] = 0;
            $dat[$value->dat]['Service Charge'] = 0;
            $dat[$value->dat]['Consultation Fee'] = 0;
            $dat[$value->dat]['Total Bill'] = 0;
            $dat[$value->dat]['Amount Paid'] = 0;
        }
        #Value loop
        foreach ($data as $key => $value) {
            $dat[$value->dat][$value->group] = $value->sum;
        }

        $cons = 0;
        $ser = 0;
        $phar = 0;
        $lab = 0;
        $cash = 0;
        $credit = 0;
        $forgive = 0;

        foreach ($dat as $key => $value) {

            $va = DB::table('attendance_payments')
                ->select(DB::raw('date(created_at) as dat'), DB::raw('sum(paid) as sum'))
                ->groupBy(DB::raw('date(created_at)'))
                ->where(DB::raw('date(created_at)'), '=', $key)
                ->where('mode', '=', 'Cash')
                ->get();

            $va2 = DB::table('attendance_payments')
                // ->select('created_at', 'amount','group')
                ->select(DB::raw('date(created_at) as dat'), DB::raw('sum(paid) as sum'))
                ->where('mode', '!=', 'Cash')
                ->where(DB::raw('date(created_at)'), '=', $key)
                // ->groupBy(DB::raw('date(created_at)'),'group')
                ->orderby('created_at', 'desc')
                ->get();

            $tempCash = (isset($va[0])) ? $va[0]->sum : 0;
            $tempCredit = (isset($va2[0])) ? $va2[0]->sum : 0;

            $cons += $value['Consultation Fee'];
            $ser += $value['Service Charge'];
            $phar += $value['Pharmacy'];
            $lab += $value['Laboratory'];
            $cash += $tempCash;
            $credit += $tempCredit;
            $forgive += $value['Laboratory'] + $value['Pharmacy'] + $value['Service Charge'] + $value['Consultation Fee'] - $tempCash - $tempCredit;

        }

        $data = array(
            'cons' => $cons,
            'ser' => $ser,
            'lab' => $lab,
            'phar' => $phar,
            'for' => $forgive,
            'cash' => $cash,
            'credit' => $credit,
            'forgive' => $forgive,
            'from' => $from,
            'to' => $to,
        );

        #Expense Section
        $expenseTransactions = ExpenseTransaction::whereRaw('date(created_at) >= ?', [$from])
            ->whereRaw('date(created_at) <= ?', [$to])
            ->orderby('created_at', 'desc')
            ->get();
        $amount = [];
        $expenseIDs = [];
        $expenses = [];
        foreach ($expenseTransactions as $expenseTransaction) {

            if (in_array($expenseTransaction->expense_id, $expenseIDs)) {
                $amount[$expenseTransaction->expense->id] += $expenseTransaction->amount;
            } else {
                $amount[$expenseTransaction->expense->id] = 0;
                $amount[$expenseTransaction->expense->id] += $expenseTransaction->amount;
                $expenseIDs[] = $expenseTransaction->expense_id;
                $expenses[$expenseTransaction->expense_id] = $expenseTransaction->expense;
            }
        }
        $expenseTransactions1 = [];
        foreach ($expenseIDs as $expenseID) {
            $expense = $expenses[$expenseID];
            $expense->total = $amount[$expenseID];
            $expenseTransactions1[] = $expense;
        }

        $data['expenses'] = $expenseTransactions1;
        #End Expense


//		return $data;
        $_title = 'Analysis';
        $_sub_title = 'Profit';
        $_page_index = 'report';
        return view('manager.income_statement', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

        // return $dat;
    }


    public function procedure_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'procedure/get'
        );
        $data = array(
            'text_string' => '<a class="btn btn-primary" href="' . url('procedure/edit/0') . '">Add New</a> <hr>',
            'table' => (object)array(
                'columns' => array(
                    'Name',
                    'Price',
                    'Option',
                )
            )
        );
        $_title = 'Insurances';
        $_sub_title = '';
        $_page_index = 'service';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function procedure_index_get()
    {
        $l = DB::table('procedures');

        $l->select('id', 'name', 'price');
        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('procedure/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
            ->removeColumn('id')
            ->toJson();
    }


    public function procedure_edit($id)
    {
        $data = array(
            'status' => 'New',
        );
        $sub_title = '<i class="icon-plus"></i>';
        if ($id == 0) {
            $title = "Create New Procedure";
            $page_index = 'procedure';
        } else {
            $l = Procedure::find($id);
            $title = "Edit Procedure";
            $page_index = 'procedure';

            $data = array(
                'procedure' => $l,
                'status' => 'Edit'
            );
        }

        $_title = $title;
        $_sub_title = $sub_title;
        $_page_index = 'service';
        return view('manager.procedure', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }


    public function procedure_edit_save($id)
    {
        // return request()->all();
        if ($id == 0) {
            $l = new Procedure();
        } else {
            $l = Procedure::find($id);
        }
        $l->name = request('name');
        $l->price = request('price');
        $l->save();

        if ($id > 0) {
            $i_id = request('insurance_id');
            $i = -1;
            foreach ($i_id as $key => $value) {
                ++$i;
                $ip = InsurancePrice::find(request('insurance_id')[$i]);
                $ip->price = request('insurance_price')[$i];
                $ip->save();
            }
        }


        return Redirect::to('service/procedure')->with('success', 'Record Saved');
    }


    public function insurance_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'insurance/get'
        );

        $temp = '';
        $temp .= '<a class="btn btn-primary" href="' . url('insurance/edit/x') . '"> Cash sponsorship prices </a> <hr>';
        $temp .= '<a class="btn btn-primary" href="' . url('insurance/edit/0') . '"> Add New </a> <hr>';

        $data = array(
//            'text_string' => '<a class="btn btn-primary" href="' . url('insurance/edit/0') . '">Add New</a> <hr>',
            'text_string' => $temp,
            'table' => (object)array(
                'columns' => array(
                    'Name',
                    'General Practioner Fee',
                    'Super Specialist Consultation Fee',
                    'Option',
                )
            )
        );
        $_title = 'Insurances';
        $_sub_title = '';
        $_page_index = 'service';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function insurance_index_get()
    {
        $l = DB::table('insurances');

        $l->select('id', 'name', 'consultation_fee', 'specialist_fee');
        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('insurance/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
            ->removeColumn('id')
            ->toJson();
    }


    public function insurance_edit($id)
    {
        $data = array(
            'status' => 'New',
        );
        $sub_title = '<i class="icon-plus"></i>';
        if ($id == 0) {
            $title = "Create New Insurance";
            $page_index = 'insurance';
        }
        if ($id == 'x') {

            $s = Setting::find(1);

            $m = new Insurance();
            $m->name = 'Cash';
            $m->consultation_fee = $s->doctor_rate;
            $m->specialist_fee = $s->specialist_rate;

            $title = "Edit Cash Prices";
            $page_index = 'insurance';

            $data = array(
                'insurance' => $m,
                'status' => 'Edit'
            );

        } else {
            $m = Insurance::find($id);
            $title = "Edit Insurance";
            $page_index = 'insurance';

            $data = array(
                'insurance' => $m,
                'status' => 'Edit'
            );
        }

        $_title = $title;
        $_sub_title = $sub_title;
        $_page_index = 'service';
        return view('manager.insurance', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

    }

    public function insurance_edit_save($id)
    {


        if ($id == "x") {

            $s = Setting::find(1);
            $s->doctor_rate = request('consultation_fee');
            $s->specialist_rate = request('specialist_fee');
            $s->save();

            return Redirect::to('service/insurance')->with('success', 'Record Saved');

        } elseif ($id == 0) {

            $l = new Insurance();


        } else {
            $l = Insurance::find($id);
        }


        $l->name = request('name');
        $l->consultation_fee = request('consultation_fee');
        $l->specialist_fee = request('specialist_fee');
        $l->save();

        if ($id == 0) {
            $i = new InsurancePrice();
            $i = $i->where('insurance_id', '=', 1)->get();

            foreach ($i as $key => $value) {
                $t = new InsurancePrice();
                $t->insurance_id = $l->id;
                $t->type = $value->type;
                $t->target_id = $value->target_id;
                $t->price = $value->price;
                $t->save();
            }

        }


        return Redirect::to('service/insurance')->with('success', 'Record Saved');
    }


    public function ward_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'wards/get'
        );
        $data = array(
            'title' => 'Patient',
            'text_string' => '<a class="btn btn-primary" href="' . url('ward/edit/0') . '">Add New</a> <hr>',
            'table' => (object)array(
                'columns' => array(
                    'Ward ID',
                    'Title',
                    'Price',
                    'Option',
                )
            )
        );
        $_title = 'Wards';
        $_sub_title = '';
        $_page_index = 'service';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function ward_index_get()
    {
        $l = DB::table('wards');

        $l->select('id', 'name', 'price');
        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('ward/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';
            // $opt .= '<a class="opt" href="{{url('ward/view/')}}/{{ $id }}"><i class="icon-eye-open"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
            ->rawColumns(['options'])
        ->toJson();
    }

    public function ward_edit($id)
    {
        $data = array(
            'status' => 'New',
        );
        $sub_title = '<i class="icon-plus"></i>';
        if ($id == 0) {
            $title = "Create New Ward";
            $page_index = 'ward';
        } else {
            $m = Ward::find($id);
            $title = "Edit Wards";
            $page_index = 'ward';

            $data = array(
                'ward' => $m,
                'status' => 'Edit'
            );
        }

        $_title = $title;
        $_sub_title = $sub_title;
        $_page_index = 'service';
        return view('manager.ward', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function ward_edit_save($id)
    {
        if ($id == 0) {
            $l = new Ward();
        } else {
            $l = Ward::find($id);
            $l->creator_id = Auth::user()->id;
        }
        $l->name = request('name');
        $l->price = request('price');
        $l->updator_id = Auth::user()->id;
        $l->save();

        return Redirect::to('service/wards')->with('success', 'Record Saved');

    }


}
