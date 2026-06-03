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

class PaymentController extends Controller
{


	function __construct() {}

	public function index()
	{
		$_layout_table = (object)array(
			'type' => 'server',
			'url' => 'payments/get'
		);
		$data = array(
			'title' => 'Patient',
			'table' => (object)array(
				'columns' => array(
					'Patient',
					'Gender',
					'Bill',
					'Paid',
					'Mode',
					'Cashier',
					'Date',
					'Report',
				)
			)
		);
		$_title = 'Patient Payment';
		$_sub_title = '<i class="icon-money"></i>';
		$_page_index = 'report';
		return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
	}

	public function index_get()
	{
		$l = DB::table('attendance_payment_data');

		$l->select('id', 'patient', 'gender', 'bill', 'paid', 'mode', 'creator', 'created_at');
		if (Auth::user()->role_id != 6) {
			$l->where('creator_id', '=', Auth::user()->id);
		}

		return DataTables::of($l)
			->addColumn('view', function ($row) {
				return '<a target="_new" class="opt" href="' . url('receipt/view/' . $row->id) . '"><i class="icon-eye-open"></i></a>';
			})
			->rawColumns(['view'])
			->editColumn('created_at', function ($row) {
				return date('H:i, d M Y', strtotime($row->created_at));
			})
			->editColumn('paid', function ($row) {
				return number_format($row->paid);
			})
			->editColumn('bill', function ($row) {
				return number_format($row->bill);
			})
			->removeColumn('id')->toJson();
	}


	public function show_receipt1($id)
	{
		$p = AttendancePayment::find($id);
		if ($p) {
			return view('receipt', array('p' => $p));
		}

		return '';
	}


	public function payment($id)
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
			return view('patient.payment', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
		}
	}


	public function payment_action($id)
	{
		$a = PatientAttendance::find($id);
		$p = Patient::find($a->patient_id);
		$i = -1;
		if ($a && $p) {

			// return request()->all();

			$ab = new AttendanceBill();
			$ab = $ab->where('attendance_id', '=', $a->id);
			$ab = $ab->where('status', '=', 'unpaid');
			$ab = $ab->get();
			$sum = 0;
			foreach ($ab as $key => $value) {
				$sum += $value->amount;
				$value->status = 'paid';
				$value->save();
			}

			if ($a->insurance_id > 0) {

				$pay = $sum;
				$a_pay = $pay;

				$ap = new AttendancePayment();
				$ap->attendance_id = $a->id;
				$ap->creator_id = Auth::user()->id;
				$ap->bill = $sum;
				$ap->insurance_id = $a->insurance_id;
				$ap->paid = $a_pay;
				$ap->tendered = $pay;
				$ap->mode = Insurance::find($a->insurance_id)->name;
				$ap->save();
			} else {

				$pay = str_replace(',', '', request('paid'));
				$a_pay = $pay;
				if ($pay > $sum) {
					$a_pay = $sum;
				}

				$ap = new AttendancePayment();
				$ap->attendance_id = $a->id;
				$ap->creator_id = Auth::user()->id;
				$ap->bill = $sum;
				$ap->paid = $a_pay;
				$ap->tendered = $pay;
				$ap->mode = "Cash";
				$ap->save();
			}

			$dis = null;

			foreach ($a->medicals as $key => $value) {
				if ($value->paid == 0) {
					$med = Medical::find($value->medical_id);
					$st = $med->getStock();
					$med->stock = $st - $value->quantity;
					$med->save();

					$dis = $value->checker_id;

					$ms = new MedicalStock();
					$ms->medical_id = $med->id;
					$ms->changes = -1 * $value->quantity;
					$ms->stock = $st - $value->quantity;
					$ms->remark = 'sale';
					$ms->payment_id = $ap->id;
					$ms->creator_id = $dis;
					$ms->save();
				}
			}

			$ap->dispense_id = $dis;
			$ap->save();

			$a->closed = 1;
			$a->closer_id = Auth::user()->id;
			$a->save();

			$p->location_office_id = '';
			$p->save();

			#Clean Attendance
			AttendanceMovement::clean($a->id);

			return Redirect::to('receipt/view/' . $ap->id);
		}
	}



	public function bill_add($id)
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

	public function bill_add_save($id)
	{
		if ($id == 0) {
			$l = new AttendanceBill();
		} else {
			$l = AttendanceBill::find($id);
		}
		$l->amount = request('amount');
		$l->save();

		if ($l->name == 'Consultation Fee') {
			$_a = $l->attendance;
			$_a->consultation_fee = $l->amount;
			$_a->save();
		}

		return Redirect::to('dashboard')->with('success', 'Record Saved');
	}
}
