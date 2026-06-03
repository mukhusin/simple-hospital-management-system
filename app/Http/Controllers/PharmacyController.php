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

class PharmacyController extends Controller
{


    function __construct()
    {
    }

    public function dispense($id)
    {
        $sub_title = '<i class="icon-user-md"></i>';
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        if ($a && $p) {

            $_title = $p->name;
            $_sub_title = $sub_title;
            $_page_index = 'patient';
            $data = array(
                'patient' => $p,
                'attend' => $a,
            );
            return view('patient.dispense', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

        }
    }

    public function dispense_action($id)
    {
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        $i = -1;
        if ($a && $p) {

            // return request()->all();

            foreach ($a->medicals as $key => $value) {
                if ($value->paid == 0) {
                    $med = Medical::find($value->medical_id);

                    if (request($med->id, 0) > 0) {

                        $value->checked = 1;
                        $value->quantity = request($med->id);
                        $value->checker_id = Auth::user()->id;
                        $value->save();

                        $temp1 = new AttendanceBill();
                        $temp1->attendance_id = $a->id;
                        $temp1->name = $med->name . ' - ' . $med->brand;
                        $temp1->dosage = $value->dosage;
                        $temp1->amount = $med->price($a->id) * request($med->id);
                        $temp1->status = "unpaid";
                        $temp1->group = "Pharmacy";
                        $temp1->save();

                    }
                    // else {
                    // 	return Redirect::to('dashboard')->with('error','Select Patient Again');
                    // }

                }
            }

            if ($a->ward_served == 1) {
                #In Patient
                $p->location_office_id = 3;
                $p->save();

                #Clean Attendance
                $m = AttendanceMovement::clean($a->id);

                $ca = $a->LastClinical();
                $ca->ward_served = $a->ward_served;
                $ca->ward_id = $a->ward_id;
                $ca->bed = $a->bed;
                $ca->save();

                #Attendance Movement
                $m = new AttendanceMovement();
                $m->attendance_id = $a->id;
                $m->from_id = Auth::user()->id;
                $m->from_office_id = Auth::user()->office_id;
                $m->to_office_id = 3;
                $m->notes = '';
                $m->status = 1;
                $m->save();
            } else {
                #Out Patient
                $p->location_office_id = 6;
                $p->save();

                $ca = $a->LastClinical();
                $a->ward_served = $ca->ward_served;
                $a->ward_id = $ca->ward_id;
                $a->bed = $ca->bed;
                $a->save();

                #Clean Attendance
                $m = AttendanceMovement::clean($a->id);

                #Attendance Movement
                $m = new AttendanceMovement();
                $m->attendance_id = $a->id;
                $m->from_id = Auth::user()->id;
                $m->from_office_id = Auth::user()->office_id;
                $m->to_office_id = 6;
                $m->notes = '';
                $m->status = 1;
                $m->save();

            }


            return Redirect::to('dashboard')->with('success', 'Patient Served');

        }
    }

    public function med_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'pharmacy_medicines/get'
        );
        $data = array(
            'title' => 'Patient',
            'text_string' => '',
//            'text_string' => '<a class="btn btn-primary" href="' . url('pharmacy_medicines/edit/0') . '">Add New</a> <hr>',
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
            $opt = '<a class="opt" href="' . url('pharmacy_medicines/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';
            $opt .= '<a class="opt" href="' . url('pharmacy_medicines/view/' . $row->id) . '"><i class="icon-eye-open"></i></a>';

                return $opt;
            })
            ->rawColumns(['options'])
            // ->toJson();
            ->removeColumn('id')->toJson();
    }

    public function emergence_dispense_list()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'emergence/dispense/get'
        );
        $data = array(
            'title' => 'Patient',
            'table' => (object)array(
                'columns' => array(
                    'Queued',
                    'Patient',
                    'Gender',
                    'Office',
                    'Option',
                )
            )
        );
        $_title = 'Medical Emergence Request';
        $_sub_title = '<i class="icon-user"></i>';
        $_page_index = 'emergence';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function emergence_dispense_lis_get()
    {
        $l = DB::table('emergence_medical_request');

        $l->select('updated_at', 'patient_id', 'attendance_id', 'patient', 'gender', 'from_office');

        return DataTables::of($l)
            ->addColumn('options', function($row) {
            $opt = '<a class="opt" href="' . url('emergence/dispense' . $row->attendance_id) . '"> Save </a>';

                return $opt;
            })
            ->rawColumns(['options'])
            ->editColumn('updated_at', function($row) {
            return date('H:i, d M Y',strtotime($row->updated_at));
        })
            ->removeColumn('patient_id')
            ->removeColumn('attendance_id')
            ->toJson();
    }

    public function emergence_dispense($id)
    {
        $sub_title = '<i class="icon-user-md"></i>';
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        if ($a && $p) {

            $_title = $p->name;
            $_sub_title = $sub_title;
            $_page_index = 'patient';
            $data = array(
                'patient' => $p,
                'attend' => $a,
            );
            return view('patient.dispense1', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

        }
    }

    public function emergence_dispense_action($id)
    {
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        $i = -1;
        if ($a && $p) {

            // return request()->all();

            foreach ($a->medicals as $key => $value) {
                if ($value->paid == 0 && $value->emergence == 1) {
                    $med = Medical::find($value->medical_id);

                    if (request($med->id, 0) > 0) {

                        $value->checked = 1;
                        $value->quantity = request($med->id);
                        $value->checker_id = Auth::user()->id;
                        $value->save();


                        $temp1 = new AttendanceBill();
                        $temp1->attendance_id = $a->id;
                        $temp1->name = $med->name . ' - ' . $med->brand . ' ( Emergence )';
                        $temp1->dosage = $value->dosage;
                        $temp1->amount = $med->price($a->id) * request($med->id);
                        $temp1->status = "unpaid";
                        $temp1->group = "Pharmacy";
                        $temp1->save();

                    } else {

                        return Redirect::to('dashboard')->with('error', 'Select Patient Again');

                    }


                }
            }

            return Redirect::to('dashboard')->with('success', 'Patient Served');
        }
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

        return Redirect::to('pharmacy_medicines')->with('success', 'Record Saved');
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
                'source' => url('pharmacy_medicines/stock/get/'.$m->id)
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

}