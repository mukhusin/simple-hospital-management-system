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

class UserController extends Controller {


	function __construct() {
	}

	public function index()
	{
		$data = array();

		if (Auth::user()->role_id == 6) {
			$Na = DB::table('patient_attendances_data')
				->select(DB::raw('year(created_at) as y, month(created_at) as m, count(id) as sum, reattend as status'))
				// ->where('reattend','=',0)
				->groupBy(DB::raw('year(created_at)'),DB::raw('month(created_at)'),'reattend')
				->orderby('y','desc')
				->limit(6)
				->get();

			$revenue = DB::table('attendance_payments')
				->select(DB::raw('year(created_at) as y, month(created_at) as m, sum(paid) as sum'))
				->groupBy(DB::raw('year(created_at)'),DB::raw('month(created_at)'))
				->orderby('y','desc')
				->limit(6)
				->get();

			$Mon = array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");
			foreach ($Na as $key => $value) {
				$temp[$Mon[$value->m].'-'.$value->y][$value->status] = $value->sum;
			}

			$data = array(
				'total' => $temp,
				'revenue' => $revenue,
			);
		}
		if (Auth::user()->role_id == 4) {
			#Patient New Sample 
			$l = DB::table('patient_queue');
			$l = $l->where('status','=',1);
			$l->where('to_office_id','=',Auth::user()->office_id);
			$r = $l->get();

			$data1 = $data2 = array();
			foreach ($r as $key => $value) {
				$a_temp = PatientAttendance::find($value->attendance_id);
				$test = explode(',', $a_temp->test);
				$test_list = array();
				foreach ($test as $key1 => $value1) {

					$c_temp = $a_temp->LastClinical();

					$al = new AttendanceLab();
					$al = $al->where('attendance_id','=',$a_temp->id);
					$al = $al->where('clinical_id','=',$c_temp->id);
					$al = $al->where('test_id','=',$value1);
					$al = $al->first();

					$test_list[] = $value1;

					// return $a_temp;
					// return $c_temp;
					// return $al;

					if (is_null($al)) {
						$lt = LabTest::find($value1);
						$temp = (object)array(
							'time' => date('H:i D, d M Y',strtotime($value->created_at)),
							'a_id' => $a_temp->id,
							't_id' => $value1,
							'patient' => $value->patient,
							'gender' => $value->gender,
							'from_user' => $value->from_user,
							'test' => $lt->uom.' - '.$lt->name,
						);
						$data1[] = $temp;
					}
					else if ($al->results_register == null || $al->results_register == '') {
						$lt = LabTest::find($value1);
						$temp = (object)array(
							'time' => date('H:i D, d M Y',strtotime($al->created_at)),
							'a_id' => $a_temp->id,
							'al' => $al,
							't_id' => $value1,
							'patient' => $value->patient,
							'gender' => $value->gender,
							'from_user' => $value->from_user,
							'sample' => $al->sample_register,
							'test' => $lt->uom.' - '.$lt->name,
						);

						$data2[] = $temp;
					}
					else if ($al->results_register == 'Skipped' && $a_temp->updated_at > $al->updated_at) {
						// return $al;
						$al->delete();

						$lt = LabTest::find($value1);
						$temp = (object)array(
							'time' => date('H:i D, d M Y',strtotime($value->created_at)),
							'a_id' => $a_temp->id,
							't_id' => $value1,
							'patient' => $value->patient,
							'gender' => $value->gender,
							'from_user' => $value->from_user,
							'test' => $lt->uom.' - '.$lt->name,
						);
						$data1[] = $temp;
					}
				}


				$c_temp = $a_temp->LastClinical();

				$al = new AttendanceLab();
				$al = $al->where('attendance_id','=',$a_temp->id);
				$al = $al->where('clinical_id','=',$c_temp->id);
				$al = $al->whereNotIn('test_id',$test_list);
				$al = $al->get();

				foreach ($al as $key1 => $value1) {
					if ($value1->results_register == null || $value1->results_register == '') {
						$lt = LabTest::find($value1->test_id);
						$temp = (object)array(
							'time' => date('H:i D, d M Y',strtotime($value1->created_at)),
							'a_id' => $a_temp->id,
							'al' => $value1,
							't_id' => $value1->test_id,
							'patient' => $value->patient,
							'gender' => $value->gender,
							'from_user' => $value->from_user,
							'sample' => $value1->sample_register,
							'test' => $lt->uom.' - '.$lt->name,
						);
						$test_list[] = $value1->test_id;

						$data2[] = $temp;
					}
				}

				if (count($al) > 0) {
					$a_temp->test = implode(',', $test_list);
					$a_temp->save();
				}

			}

			$data['data1'] = $data1;
			$data['data2'] = $data2;
					
		}
		$_title = 'Dashboard';
		$_sub_title = '<i class="icon-home"></i>';
		$_page_index = 'dashboard';
		return view('dashboard', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '']));
 	}

	public function setting()
	{
		$_title = 'Account Setting';
		$_sub_title = '<i class="icon-user-md"></i>';
		$_page_index = 'setting';
		return view('user.setting', ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '']);
	}

	public function setting_action()
	{
		$inputs = request()->all();
		// return $inputs;
		$rules = array(
			'password_1' => 'required',
			'password_2' => 'same:password_1'
		);
		$val = Validator::make($inputs, $rules);
		if ($val->fails()) {
			$message = 'Password does not match';
			foreach ($val->messages() as $key => $value) {
				$message .= $value;
			}
			return Redirect::to('settings')->with('error',$message);
		}
		else {
			$u = Auth::user();
			$u->password = Hash::make(request('password_1'));
			$u->save();
			return Redirect::to('settings')->with('success','Password changed successfully');
		}
	}

	public function change_password()
	{
		$inputs = request()->all();
		// return $inputs;
		$rules = array(
			'password_1' => 'required',
			'password_2' => 'same:password_1'
		);
		$val = Validator::make($inputs, $rules);
		if ($val->fails()) {
			#Error
			$message = 'Password does not match';
			foreach ($val->messages() as $key => $value) {
				$message .= $value;
			}
			return Redirect::to('account')->with('error',$message);
		}
		else {
			$u = Auth::user();
			$u->password = Hash::make(request('password_1'));
			$u->save();
			return Redirect::to('account')->with('success','password changed successfully');
		}
	}

	public function logout()
	{
		Auth::logout();
		return Redirect::to('login');
	}

}