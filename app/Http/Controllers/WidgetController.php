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

class WidgetController extends Controller {

	public function inoffice_get()
	{
		$l = DB::table('patient_queue');

		$l = $l->where('status','=',1);

		$l->select('patient_id','updated_at','from_office','patient','gender');
		$l->where('to_office_id','=',Auth::user()->office_id);

		return DataTables::of($l)
			->addColumn('options', function($row) {
    		$opt = '<a class="opt" href="' . url('patient/view/' . $row->patient_id) . '"><i class="icon-eye-open"></i></a>';

			    return $opt;
			})
			->rawColumns(['options'])
			->editColumn('updated_at', function($row) {
            return date('H:i, d M Y',strtotime($row->updated_at));
        })
			->removeColumn('patient_id')->toJson();
		
	}


	public function inoffices_get()
	{
		$l = DB::table('patient_queue');

		$l = $l->where('status','=',1);

		$l->select('patient_id','attendance_id','patient','gender','to_office');
		// $l->where('to_office_id','=',Auth::user()->office_id);

		return DataTables::of($l)
			->addColumn('options', function($row) {
    		$opt = '<a class="opt" href="' . url('patient/view/' . $row->patient_id) . '"><i class="icon-eye-open"></i></a>';

    		if (Auth::user()->role_id == 3) {
    			$opt .= '<a class="opt" href="' . url('patient/emergence/' . $row->attendance_id) . '">Emergence</a>';
    		}

            if (Auth::user()->role_id == 6) {
                $opt .= '<a class="opt" href="' . url('patient/attendance/close/' . $row->attendance_id) . '"> Checkout </a>';
            }

			    return $opt;
			})
			->rawColumns(['options'])
			->removeColumn('attendance_id')
			->toJson();
		
	}


	public function bills_get()
	{
		$l = DB::table('patient_bills');

		$l->where('closed','=',0);
		$l->where('status','=','unpaid');

		$l->select('id','patient_id','patient','gender','name','amount');
		// $l->where('to_office_id','=',Auth::user()->office_id);
			
		return DataTables::of($l)
			->addColumn('options', function($row) {
    		$opt = '';
    		if (Auth::user()->role_id == 6) {#manager
    			$opt = '<a class="opt" href="' . url('bill/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';
    		}
    		else if (Auth::user()->role_id == 1) {#reception
    			$opt = '<a class="opt" href="' . url('bill/pay/' . $row->id) . '"> + pay<a>';
    		}
		
			    return $opt;
			})
			->rawColumns(['options'])
			->removeColumn('id')
			->rawColumns(['options'])
        ->toJson();
		
	}


}