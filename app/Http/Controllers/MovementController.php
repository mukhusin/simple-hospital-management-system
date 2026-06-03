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

class MovementController extends Controller
{


	function __construct() {}

	public function start_attendance($id = '')
	{
		$p = Patient::find($id);
		if ($p) {
			// return request()->all();


			$a = new PatientAttendance();
			$a->patient_id = $p->id;
			$a->creator_id = Auth::user()->id;
			$a->save();

			$pay = request('payment', null);

			if (is_numeric($pay) && $pay >= 1) {
				$a->insurance_id = $pay;
				$a->insurance_number = $p->sponsor_code;
				$a->save();
			}


			$p->location_office_id = request('office');
			$p->save();

			$m = new AttendanceMovement();
			$m->attendance_id = $a->id;
			$m->from_id = Auth::user()->id;
			$m->from_office_id = Auth::user()->office_id;
			$m->to_office_id = request('office');
			$m->notes = request('notes', 'start attendance');
			$m->status = 1;
			$m->save();


			$ab = new AttendanceBill();
			$ab->attendance_id = $a->id;
			$ab->name = 'Consultation Fee';

			if ($m->to_office_id <= 2) {
				InSession::record();
				$status = User::find(InSession::getUserId($m->to_office_id))->status;
				$s = Setting::find(1);
				if ($m->to_office_id == Office::nonspecialist_id) {
					$ab->status = "unpaid";
					$ab->amount = $s->doctor_rate;
					$a->consultation_fee = $s->doctor_rate;
				} else {
					$ab->status = "unpaid";
					$ab->amount = $s->specialist_rate;
					$a->consultation_fee = $s->specialist_rate;
				}

				#Special Group
				if ($a->insurance_id > 0) {
					$in = Insurance::find($a->insurance_id);
					if ($m->to_office_id == Office::nonspecialist_id) {
						$ab->amount = $in->consultation_fee;
						$a->consultation_fee = $in->consultation_fee;
					} else {
						$ab->amount = $in->specialist_fee;
						$a->consultation_fee = $in->specialist_fee;
					}
				}
				#End Special Group

				$a->save();
			}


			$ab->group = "Consultation Fee";
			$ab->save();

			return Redirect::to('dashboard')
				->with('success', 'Patient have been queued for service');
		}
	}

	public function reattend_attendance($id = '')
	{
		$p = Patient::find($id);
		if ($p) {

			$a = new PatientAttendance();
			$a->patient_id = $p->id;
			$a->reattend = 1;
			$a->creator_id = Auth::user()->id;
			$a->consultation_fee = 0;
			$a->save();

			$pay = request('payment', null);

			if (is_numeric($pay) && $pay >= 1) {
				$a->insurance_id = $pay;
				$a->insurance_number = $p->sponsor_code;
				$a->save();
			}

			$p->location_office_id = request('office');
			$p->save();

			$m = new AttendanceMovement();
			$m->attendance_id = $a->id;
			$m->from_id = Auth::user()->id;
			$m->from_office_id = Auth::user()->office_id;
			$m->to_office_id = request('office');
			$m->notes = request('notes', 'start attendance');
			$m->status = 1;
			$m->save();

			$ab = new AttendanceBill();
			$ab->attendance_id = $a->id;
			$ab->name = 'Consultation Fee';
			$ab->status = "unpaid";
			$ab->amount = 0;
			$ab->group = "Consultation Fee";
			$ab->save();

			return Redirect::to('dashboard')
				->with('success', 'Patient have been queued for service');
		}
	}

	public function return_action($id)
	{
		$a = PatientAttendance::find($id);
		$p = Patient::find($a->patient_id);
		if ($a && $p) {
			$loc = $a->doctor->office_id;

			$p->location_office_id = $loc;
			$p->save();

			#Clean Attendance
			$m = AttendanceMovement::clean($a->id);

			#Attendance Movement
			$m = new AttendanceMovement();
			$m->attendance_id = $a->id;
			$m->from_id = Auth::user()->id;
			$m->from_office_id = Auth::user()->office_id;
			$m->to_office_id = $loc;
			$m->notes = '';
			$m->status = 1;
			$m->save();

			return Redirect::to('patient/view/' . $p->id)->with('success', 'Patient Information Saved');
		}
	}

	public function close_attendance($id)
	{

		$a = PatientAttendance::find($id);
		$p = Patient::find($a->patient_id);
		$a->closed = 1;
		$a->closer_id = Auth::user()->id;
		$a->save();

		$p->location_office_id = '';
		$p->save();

		return Redirect::to('patient/view/' . $p->id)->with('success', 'Patient Information Saved');
	}


	public function index()
	{
		$_layout_table = (object)array(
			'type' => 'server',
			'url' => 'movements/get'
		);
		$data = array(
			'title' => 'Patient',
			'table' => (object)array(
				'columns' => array(
					'Patient',
					'Gender',
					'From Office',
					'From User',
					'To Office',
					'Time',
				),
				'column_keys' => ['patient', 'gender', 'from_office', 'from_user', 'to_office', 'created_at'],
			)
		);
		$_title = 'Live Patient Movement';
		$_sub_title = '<i class="icon-exchange"></i>';
		$_page_index = 'movement';
		return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
	}

	public function index_get()
	{
		$l = DB::table('attandance_movement_data');

		$l->select('id', 'patient', 'gender', 'from_office', 'from_user', 'to_office', 'created_at');
		// $l->where('creator_id','=',Auth::user()->id);

		return DataTables::of($l)
			->editColumn('created_at', function ($row) {
				return date('H:i, D', strtotime($row->created_at));
			})
			->removeColumn('id')->toJson();
	}


	public function attendance_index()
	{
		$_layout_table = (object)array(
			'type' => 'server',
			'url' => 'report/attendances/get'
		);
		$data = array(
			'title' => 'Patient',
			'table' => (object)array(
				'columns' => array(
					'Patient',
					'Gender',
					'Session Start',
					'Session End',
					'Doctor Serve',
				),
				'column_keys' => ['patient', 'gender', 'created_at', 'updated_at', 'doctor'],
			)
		);
		$_title = 'Patient Attendence History';
		$_sub_title = '<i class="icon-tasks"></i>';
		$_page_index = 'report';
		return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
	}

	public function attendance_index_get()
	{
		$l = DB::table('patient_attendances_data');

		$l->select('id', 'patient', 'gender', 'created_at', 'updated_at', 'doctor');

		if (Auth::user()->office_id == 6) {
			$l->where('creator_id', '=', Auth::user()->id);
		}

		return DataTables::of($l)
			->editColumn('created_at', function ($row) {
				return date('H:i, D d-M-Y', strtotime($row->created_at));
			})
			->editColumn('updated_at', function ($row) {
				return date('H:i, D d-M-Y', strtotime($row->updated_at));
			})
			->removeColumn('id')->toJson();
	}


	public function history_index()
	{
		$n = Date('m');
		for ($i = 5; $i >= 0; $i--) {
			$k = $n + (-1 * $i);
			if ($k <= 12) {
				$dat[] = [date('Y'), $k];
			} else {
				$dat[] = [date('Y') - 1, $k - 12];
			}
		}


		$_layout_table = (object)array(
			'type' => 'server',
			'url' => 'history/get'
		);
		$data = array(
			'title' => 'Patient',
			'table' => (object)array(
				'columns' => array(
					'Patient',
					'Gender',
					'Send To',
					'Time',
				),
				'column_keys' => ['patient', 'gender', 'to_office', 'created_at'],
			)
		);
		$_title = 'Service History';
		$_sub_title = '<i class="icon-exchange"></i>';
		$_page_index = 'history';
		return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
	}

	public function history_index_get()
	{
		$l = DB::table('attandance_movement_data');

		$l->select('id', 'patient', 'gender', 'to_office', 'created_at');
		$l->where('from_id', '=', Auth::user()->id);

		return DataTables::of($l)
			->editColumn('created_at', function ($row) {
				return date('H:i, D d M Y', strtotime($row->created_at));
			})
			->editColumn('updated_at', function ($row) {
				return date('H:i, D d M Y', strtotime($row->created_at));
			})
			->removeColumn('id')->toJson();
	}

	public function doctor_performance1()
	{

		$temp = array();

		$Na = DB::table('patient_attendances_data')
			->select(DB::raw('year(created_at) as y, month(created_at) as m, count(id) as sum, reattend as status'))
			->where('doctor_id', '=', Auth::user()->id)
			->groupBy(DB::raw('year(created_at)'), DB::raw('month(created_at)'), 'reattend')
			->orderby('created_at', 'desc')
			->limit(6)
			->get();

		$Mon = array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");
		foreach ($Na as $key => $value) {
			$temp[$Mon[$value->m] . '-' . $value->y][$value->status] = $value->sum;
		}

		$data = array(
			'total' => $temp,
		);

		// return DB::getQueryLog();

		$_title = 'Performance Report';
		$_sub_title = '<i class="icon-chart"></i>';
		$_page_index = 'peformance';
		return view('doctor.performance1', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
	}

	public function doctor_modify()
	{
		$_layout_table = (object)array(
			'type' => 'server',
			'url' => 'doctor/modify/get'
		);
		$data = array(
			'title' => 'Patient Record Modification',
			'table' => (object)array(
				'columns' => array(
					'Patient',
					'Gender',
					'Send To',
					'Time',
					'Modify Options',
				)
			)
		);
		$_title = 'Patient Record Modification';
		$_sub_title = '<i class="icon-edit"></i>';
		$_page_index = 'modify';
		return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
	}

	public function doctor_modify_get()
	{
		$l = DB::table('patient_queue');

		$l->where('from_id', '=', Auth::user()->id);
		$l->where('from_office_id', '=', Auth::user()->office_id);

		$l->whereIn('to_office_id', array(4, 5));

		$l->select('to_office_id', 'attendance_id', 'patient', 'gender', 'to_office', 'created_at');
		$l->where('from_id', '=', Auth::user()->id);

		return DataTables::of($l)
			->addColumn('option', function ($row) {
				$opt = '<a class="opt" href="' . url('attendance/clinical/edit/' . $row->attendance_id) . '"> clinical </a>';
				if ($row->to_office_id == 5) {
					$opt .= '<a class="opt" href="' . url('attendance/treatment/' . $row->attendance_id) . '">treatment</a>';
				} elseif ($row->to_office_id == 4) {
					$opt .= '<a class="opt" href="' . url('attendance/investigation/' . $row->attendance_id) . '"> investigation </a>';
				}
				return $opt;
			})
			->editColumn('created_at', function ($row) {
				return date('H:i, D d M Y', strtotime($row->created_at));
			})
			->removeColumn('attendance_id')
			->removeColumn('to_office_id')
			->toJson();
	}

	public function emergence()
	{
		$_layout_table = (object)array(
			'type' => 'server',
			'url' => 'widget/get/inoffices'
		);
		$data = array(
			'title' => 'Patient Record Modification',
			'table' => (object)array(
				'columns' => array(
					'ID',
					'Patient',
					'Gender',
					'Located',
					'Options',
				)
			)
		);
		$_title = 'Patients Emergence Medicine Request';
		$_sub_title = '<i class="icon-edit"></i>';
		$_page_index = 'emergence';
		return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
	}
}
