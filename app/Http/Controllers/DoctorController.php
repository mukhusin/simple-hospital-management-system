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

class DoctorController extends Controller
{


    function __construct() {}


    public function dia_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'doc/diagnosis/get'
        );
        $data = array(
            'title' => 'Patient',
            'text_string' => '<a class="btn btn-primary" href="' . url('doc/diagnosis/edit/0') . '">Add New</a> <hr>',
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
            $opt = '<a class="opt" href="' . url('doc/diagnosis/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';

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

        return Redirect::to('doc/diagnosis')->with('success', 'Record Saved');
    }

}