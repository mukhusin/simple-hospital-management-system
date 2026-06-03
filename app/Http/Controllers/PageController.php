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

class PageController extends Controller {


	function __construct() {}

	public function login()
	{

		foreach (AttendancePayment::last_list() as $key => $value) {
			if ($value->tendered > $value->bill) {
				$value->paid = $value->bill;
				$value->save();
			}
		}

		foreach (Medical::last_list() as $key => $value) {
			$value->stock = $value->getStock();
			$value->save();
		}

		foreach (AttendanceBill::last_list() as $key => $value) {
			if ($value->group == null || $value->group == '') {
				if ($value->name == 'Consultation Fee' || $value->name == 'Doctor Charge' ) {
					$value->group = 'Consultation Fee';
				}
				elseif ($value->name == 'Service Charge') {
					$value->group = 'Service Charge';
				}
				elseif (($value->dosage == null) && LabTest::findName($value->name)) {
					$value->group = 'Laboratory';
				}
				else{
					$value->group = 'Pharmacy';
				}
				$value->save();
			}
		}

		// Auth::logout();
		if (!Auth::guest()) {
			return Redirect::to('check');
		}

		$_title = 'System Log-In';
		return view('login');
	}

	public function login_action()
	{
		// return request()->all();
		$data = array(
			'username' => request('username'),
			'password' => request('password'),
		);
		if (Auth::attempt($data)) {
			if (Auth::user()->hide == 1) {
				Widget::refreshWidgets();
				return Redirect::to('check');
			}
			return Redirect::to('login')->with('error','Your Account have been deleted!');
		}
		else {
			return Redirect::to('login')->with('error','Wrong Username or Password!');
		}
	}

	public function about()
	{
		$j = 'search';
		$_title = '';
		return view('page.about', ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '']);
	}

}