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

class PatientController extends Controller
{


    function __construct() {}

    public function index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'patients/get'
        );
        $data = array(
            'title' => 'Patient',
            'table' => (object)array(
                'columns' => array(
                    'ID',
                    'Full Name',
                    'Gender',
                    'Phone',
                    'Registered',
                    'Options'
                ),
                'column_keys' => array(
                    'id',
                    'name',
                    'gender',
                    'phone1',
                    'created_at',
                    'options'
                )
            )
        );
        $_title = 'Patients';
        $_sub_title = '<i class="icon-user"></i>';
        $_page_index = 'patient';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function index_get()
    {
        $l = DB::table('patients');

        $l->select('id', 'name', 'gender', 'phone1', 'created_at');
        // $l->where('creator_id','=',Auth::user()->id);

        return DataTables::of($l)
            ->addColumn('options', function ($row) {
                $opt = '<a class="opt" href="' . url('patient/view/' . $row->id) . '"><i class="icon-eye-open"></i></a>';

                if (Auth::user()->role_id == 1) {
                    $opt .= '<a class="opt" href="' . url('patient/edit/' . $row->id) . '"><i class="icon-edit"></i></a>';
                }
                // $opt .= '<a class="opt" href="{{url('patient/edit/vital/')}}/{{ $id }}"><i class="icon-edit"></i> Vital</a>';

                return $opt;
            })
            ->rawColumns(['options'])
            ->editColumn('created_at', function ($row) {
                return date('d M Y', strtotime($row->created_at));
            })
            // ->removeColumn('id')
            ->toJson();
    }

    public function view($id)
    {
        $sub_title = '<i class="icon-user"></i>';
        $p = Patient::find($id);
        if ($p) {

            $_title = $p->name;
            $_sub_title = $sub_title;
            $_page_index = 'patient';
            $data = array(
                'patient' => $p,
                'attend' => PatientAttendance::getCurrent($p->id),
            );
            return view('patient.view', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
        }
    }

    public function edit($id)
    {
        $data = array(
            'status' => 'New',
        );
        $sub_title = '<i class="icon-plus"></i>';
        if ($id == 0) {
            $title = "Create Patient File";
            $page_index = 'add_patient';
        } else {
            $p = Patient::find($id);
            $title = "Edit Patient File";
            $page_index = 'patient';

            $data = array(
                'patient' => $p,
                'status' => 'Edit'
            );
        }

        $_title = $title;
        $_sub_title = $sub_title;
        $_page_index = $page_index;
        return view('patient.edit', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function edit_save($id)
    {
        // return request()->all();
        if ($id == 0) {
            $p = new Patient();
            $p->creator_id = Auth::user()->id;
        } else {
            $p = Patient::find($id);
        }

        // return request()->all();

        $p->name = ucwords(request('name'));
        $p->dob = request('dob');
        $p->gender = request('gender');
        $p->phone1 = request('phone1');
        $p->phone2 = request('phone2');
        $p->address = ucwords(request('address'));
        $p->notes = request('notes');

        $p->occupation = request('occupation');
        $p->title = request('title');
        $p->card = request('card');
        $p->sponsor = request('sponsor');
        $p->sponsor_code = request('sponsor_code');
        $p->contact_name = ucwords(request('contact_name'));
        $p->contact_phone = request('contact_phone');

        $p->country = request('country');
        $p->race = request('race');
        // $p->contact_name = request('contact_name');
        // $p->contact_name = request('contact_name');

        $p->updator_id = Auth::user()->id;
        $p->save();

        if ($id == 0) {
            $p1 = new PatientVital();
            $p1->patient_id = $p->id;
            $p1->updator_id = Auth::user()->id;
            $p1->creator_id = Auth::user()->id;
            $p1->save();
        }

        return Redirect::to('patient/view/' . $p->id)->with('success', 'Patient Record Saved');
    }

    public function change_action($id)
    {
        // return request()->all();
        $p = Patient::find($id);
        $section = request('section');
        if ($p->id > 0) {
            $a = PatientAttendance::getCurrent($p->id);

            if ($section == 'ward') {

                WardCheck::clean($a->id);

                $w = new WardCheck();
                $w->attendance_id = $a->id;
                $w->ward_id = request('ward');
                $w->bed = request('bed');
                $w->checkin = Date('Y-m-d H:i:s');
                $w->updator_id = Auth::user()->id;
                $w->creator_id = Auth::user()->id;
                $w->save();

                $a->ward_id = $w->ward_id;
                $a->bed = $w->bed;
                $a->save();
            }

            return Redirect::to('patient/view/' . $p->id)->with('success', 'Patient Record Saved');
        }
    }

    public function vital_edit($id)
    {
        $p = Patient::find($id);
        $sub_title = '<i class="icon-plus"></i>';
        if ($p) {
            $data = array(
                'patient' => $p,
            );

            $_title = $p->name . ' vital sign';
            $_sub_title = $sub_title;
            $_page_index = 'patient';

            return view('patient.vital', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
        }
    }

    public function vital_edit_save($id)
    {
        // return request()->all();
        $p = Patient::find($id);
        $sub_title = '<i class="icon-plus"></i>';
        if ($p) {

            $v = $p->vitalSign;
            $v->temperature = request('temperature');
            $v->height = request('height');
            $v->weight = request('weight');
            $v->blood = request('blood');
            $v->updator_id = Auth::user()->id;
            $v->save();
        }

        return Redirect::to('patient/view/' . $p->id)->with('success', 'Patient Record Saved');
    }


    public function allegies_edit($id)
    {
        $p = Patient::find($id);
        $sub_title = '<i class="icon-plus"></i>';
        if ($p) {
            $data = array(
                'patient' => $p,
            );

            $_title = $p->name . ' Allegies';
            $_sub_title = $sub_title;
            $_page_index = 'patient';

            return view('patient.allegies', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
        }
    }

    public function allegies_edit_save($id)
    {
        // return request()->all();
        $p = Patient::find($id);
        $sub_title = '<i class="icon-plus"></i>';
        if ($p) {

            $g = implode(',', request('allegies'));
            $p->allegies = $g;
            $p->save();
        }

        return Redirect::to('patient/view/' . $p->id)->with('success', 'Patient Record Saved');
    }

    public function in_index()
    {
        $_layout_table = (object)array(
            'type' => 'server',
            'url' => 'inpatients/get'
        );
        $data = array(
            'title' => 'Patient',
            'table' => (object)array(
                'columns' => array(
                    'ID',
                    'Full Name',
                    'Gender',
                    'Doctor',
                    'Ward',
                    'Bed',
                    'Options'
                ),
                'column_keys' => array(
                    'patient_id',
                    'patient',
                    'gender',
                    'doctor',
                    'ward',
                    'bed',
                    'options'
                )
            )
        );
        $_title = 'In Patients';
        $_sub_title = '<i class="icon-bullseye"></i>';
        $_page_index = 'inpatient';
        return view('table', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }

    public function in_index_get()
    {
        $l = DB::table('inpatients');

        $l->select('patient_id', 'patient', 'gender', 'doctor', 'ward', 'bed');
        // $l->where('creator_id','=',Auth::user()->id);

        return DataTables::of($l)
            ->addColumn('options', function ($row) {
                $opt = '<a class="opt" href="' . url('patient/view/' . $row->patient_id) . '"><i class="icon-eye-open"></i></a>';

                if (Auth::user()->role_id == 1) {
                    $opt .= '<a class="opt" href="' . url('patient/edit/' . $row->patient_id) . '"><i class="icon-edit"></i></a>';
                }
                // $opt .= '<a class="opt" href="{{url('patient/edit/vital/')}}/{{ $id }}"><i class="icon-edit"></i> Vital</a>';

                return $opt;
            })
            ->rawColumns(['options'])
            // ->removeColumn('id')
            ->rawColumns(['options'])
            ->toJson();
    }

    public function clinical($id)
    {
        $sub_title = '<i class="icon-user"></i>';
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        if ($a && $p) {

            $_title = $p->name . ' Clinical Page';
            $_sub_title = $sub_title;
            $_page_index = 'patient';
            $data = array(
                'patient' => $p,
                'attend' => $a,
            );
            return view('patient.clinical', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
        }
    }

    public function clinical_action($id, $new = true)
    {
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        if ($a && $p) {

            $provision = request('provision');
            $differential = request('differential');
            $final = request('final');

            $rules = array(
                'complain' => 'required|min:2',
                'general_observation' => 'required|min:2',
                // 'systemic_observation' => 'required|min:2',
                'provision' => 'required',
                // 'review' => 'required|min:2',
                // 'differential' => 'required|array',
                // 'final' => 'required|array',
            );

            $data = request()->all();

            // return $data;

            $v = Validator::make($data, $rules);

            if ($v->fails()) {
                // return $v->messages();
                $a->history = '-';
                $a->general_observation = request('general_observation');
                $a->systemic_observation = request('systemic_observation');
                $a->complain = request('complain');

                $a->doctor_id = Auth::user()->id;
                $a->save();

                return Redirect::to('attendance/clinical/' . $a->id)
                    ->with('error', 'Clinical Information not completed');
            } else {

                $provision = implode(',', $provision);
                if ($differential) {
                    $differential = implode(',', $differential);
                }
                if ($final) {
                    $final = implode(',', $final);
                }

                $a->history = '-';
                $a->review = request('review');
                $a->general_observation = request('general_observation');
                $a->systemic_observation = request('systemic_observation');
                $a->complain = request('complain');
                $a->remark = request('remark');

                $a->provision = $provision;
                $a->differential = $differential;
                $a->final = $final;
                $a->doctor_id = Auth::user()->id;

                $a->save();

                if ($new == true) {
                    $ac = new AttendanceClinical();
                    $ac->attendance_id = $a->id;
                    $ac->history = '-';
                    $ac->review = request('review');
                    $ac->general_observation = request('general_observation');
                    $ac->systemic_observation = request('systemic_observation');
                    $ac->complain = request('complain');
                    $ac->comment = request('comment');
                    $ac->remark = request('remark');

                    $ac->provision = $provision;
                    $ac->differential = $differential;
                    $ac->final = $final;
                    $ac->creator_id = Auth::user()->id;

                    $ac->save();
                }
            }

            // $round = AttendanceDiagnosis::count();
            // foreach (explode(',', $ad_provision) as $key => $value) {
            // 	$ad = new AttendanceDiagnosis();
            // 	$ad->attendence_id = $a->id;
            // 	$ad->diagnosis_id = $value;
            // 	$ad->tag = 'provision';
            // 	$ad->round = $round;
            // 	$ad->save();
            // }

            // foreach (explode(',', $ad_differential) as $key => $value) {
            // 	$ad = new AttendanceDiagnosis();
            // 	$ad->attendence_id = $a->id;
            // 	$ad->diagnosis_id = $value;
            // 	$ad->tag = 'differential';
            // 	$ad->round = $round;
            // 	$ad->save();
            // }

            // foreach (explode(',', $ad_final) as $key => $value) {
            // 	$ad = new AttendanceDiagnosis();
            // 	$ad->attendence_id = $a->id;
            // 	$ad->diagnosis_id = $value;
            // 	$ad->tag = 'final';
            // 	$ad->round = $round;
            // 	$ad->save();
            // }

            return Redirect::to('patient/view/' . $p->id)
                ->with('success', 'Clinical Information saved');
        }
    }

    public function clinical_edit($id = '')
    {
        return $this->clinical($id);
    }


    public function clinical_edit2($id)
    {
        $sub_title = '<i class="icon-user"></i>';
        $ac = AttendanceClinical::find($id);
        $a = $ac->attendance;
        //        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        if ($a && $p) {

            $_title = $p->name . ' Clinical Page';
            $_sub_title = $sub_title;
            $_page_index = 'patient';
            $data = array(
                'patient' => $p,
                'attend' => $a,
            );
            return view('patient.clinical', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
        }
    }

    public function clinical_edit2_action($id)
    {

        $ac = AttendanceClinical::find($id);
        $ac->history = '-';
        $ac->review = request('review');
        $ac->general_observation = request('general_observation');
        $ac->systemic_observation = request('systemic_observation');
        $ac->complain = request('complain');
        $ac->comment = request('comment');
        $ac->remark = request('remark');

        $provision = request('provision');
        $differential = request('differential');
        $final = request('final');


        if ($provision) {
            $provision = implode(',', $provision);
        }
        if ($differential) {
            $differential = implode(',', $differential);
        }
        if ($final) {
            $final = implode(',', $final);
        }

        $ac->provision = $provision;
        $ac->differential = $differential;
        $ac->final = $final;
        $ac->save();

        return Redirect::to('patient/view/' . $ac->attendance->patient_id)
            ->with('success', 'Clinical Information updated!');
    }

    public function clinical_edit_action($id = '')
    {
        return $this->clinical_action($id, false);
    }

    public function investigation($id)
    {
        $sub_title = '<i class="icon-user"></i>';
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);

        $lt = $a->LastClinical();
        if (is_null($lt)) {
            return Redirect::to('attendance/clinical/' . $a->id)
                ->with('error', 'Please enter Clinical Information first');
        }

        if ($a && $p) {
            $_title = $p->name . ' Investigation';
            $_sub_title = $sub_title;
            $_page_index = 'patient';
            $data = array(
                'patient' => $p,
                'attend' => $a,
            );
            return view('patient.investigation', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
        }
    }


    public function investigation_action($id)
    {
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        if ($a && $p) {

            $test = request('test');

            foreach ($test as $key => $value) {

                // $ab = new AttendanceBill();
                // $ab->attendance_id = $a->id;
                // $ab->name = LabTest::find($value)->name.' test';
                // $ab->amount = LabTest::find($value)->price;
                // $ab->status = "unpaid";
                // $ab->save();

            }

            $a->test = implode(',', $test);
            $a->save();

            $p->location_office_id = 4;
            $p->save();

            #Clean Attendance
            $m = AttendanceMovement::clean($a->id);

            #Attendance Movement
            $m = new AttendanceMovement();
            $m->attendance_id = $a->id;
            $m->from_id = Auth::user()->id;
            $m->from_office_id = Auth::user()->office_id;
            $m->to_office_id = 4;
            $m->notes = '';
            $m->status = 1;
            $m->save();

            return Redirect::to('dashboard')->with('success', 'Patient Served');
        }
    }


    // public function clinical($id)
    // {
    // 	$sub_title = '<i class="icon-user"></i>';
    // $a = Attendance::find($id);
    // $p = Patient::find($a->patient_ids);
    // if ($a && $p) {

    // 	$_title = $p->name;
    // 	$_sub_title = $sub_title;
    // 	$_page_index = 'patient';
    // 	$data = array(
    // 		'patient' => $p,
    // 		'attend' => $a,
    // 	);
    // 	return view('patient.clinical', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));

    // }
    // }


    public function emergence($id)
    {
        $sub_title = '<i class="icon-user"></i>';
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);

        $lt = $a->LastClinical();
        if (is_null($lt)) {
            return Redirect::to('attendance/clinical/' . $a->id)
                ->with('error', 'Please enter Clinical Information first');
        }

        if ($a && $p) {

            $_title = $p->name;
            $_sub_title = $sub_title;
            $_page_index = 'patient';
            $data = array(
                'patient' => $p,
                'attend' => $a,
            );
            return view('patient.emergence', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
        }
    }

    public function emergence_action($id)
    {
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        $i = -1;
        if ($a && $p) {

            // return request()->all();
            $medical = request('med');
            $dosage = request('dosage');

            $tem = $a->LastClinical();
            $tem->remark = request('remark');
            $tem->save();

            #Clear Medical
            $ad = new AttendanceMedical();
            $ad = $ad->where('attendance_id', '=', $a->id);
            $ad = $ad->where('emergence', '=', 1);
            $ad = $ad->where('clinical_id', '=', $a->getCurrentClinical()->id);
            $ad = $ad->delete();
            // return $ad;
            // return DB::getQueryLog();

            foreach ($medical as $key => $value) {
                if ($value > 0) {
                    ++$i;
                    $temp = Medical::find($value);
                    $ad = new AttendanceMedical();
                    $ad->attendance_id = $a->id;
                    $ad->medical_id = $value;
                    $ad->emergence = 1;
                    $ad->clinical_id = $a->getCurrentClinical()->id;
                    $ad->dosage = $dosage[$i];
                    $ad->creator_id = Auth::user()->id;
                    $ad->save();
                }
            }

            return Redirect::to('patient/view/' . $p->id)->with('success', 'Emergence Medical request sent');
        }
    }

    public function treatment($id)
    {
        $sub_title = '<i class="icon-user"></i>';
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);

        $lt = $a->LastClinical();
        if (is_null($lt)) {
            return Redirect::to('attendance/clinical/' . $a->id)
                ->with('error', 'Please enter Clinical Information first');
        }

        if ($a && $p) {

            $_title = $p->name . ' Treatment / Checkout';
            $_sub_title = $sub_title;
            $_page_index = 'patient';
            $data = array(
                'patient' => $p,
                'attend' => $a,
            );
            return view('patient.treatment', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
        }
    }


    public function treatment_action($id)
    {
        $a = PatientAttendance::find($id);
        $p = Patient::find($a->patient_id);
        $i = -1;
        if ($a && $p) {

            // return request()->all();
            $medical = request('med');
            $procedures = request('procedures');
            $dosage = request('dosage');

            $tem = $a->LastClinical();
            $tem->remark = request('remark');
            $tem->save();

            $type = request('type');

            if ($a->type == 'In' && $a->ward_id > 0 && $type == 'In') {

                if ($a->ward_id == request('ward') && $a->bed == request('bed')) {;
                } else {
                    $a->ward_id = request('ward');
                    $a->bed = request('bed');

                    WardCheck::clean($a->id);

                    $w = new WardCheck();
                    $w->attendance_id = $a->id;
                    $w->ward_id = request('ward');
                    $w->bed = request('bed');
                    $w->checkin = Date('Y-m-d H:i:s');
                    $w->updator_id = Auth::user()->id;
                    $w->creator_id = Auth::user()->id;
                    $w->save();
                }
            } else if ($type == 'In') {
                $a->type = request('type');
                $a->ward_id = request('ward');
                $a->bed = request('bed');
                $a->ward_served = 1;

                WardCheck::clean($a->id);

                $w = new WardCheck();
                $w->attendance_id = $a->id;
                $w->ward_id = request('ward');
                $w->bed = request('bed');
                $w->checkin = Date('Y-m-d H:i:s');
                $w->updator_id = Auth::user()->id;
                $w->creator_id = Auth::user()->id;
                $w->save();
            } else if ($type == 'Out') {
                $a->ward_id = NULL;
                $a->bed = NULL;
                WardCheck::clean($a->id);
                // return $a;
            }
            $a->type = $type;

            $a->ward_served = 0;
            if ($a->type == 'In') {
                $a->ward_served = 1;
            }

            $a->followup = request('followup');
            $a->followup_time = request('followup_time');
            $a->service_amount = str_replace(',', '', request('service_amount', 0));
            $a->service_remark = request('service_remark');
            $a->save();

            if ($a->service_amount > 0) {
                $temp1 = new AttendanceBill();
                $temp1->attendance_id = $a->id;
                $temp1->name = 'Service Charge';
                $temp1->dosage = $a->service_remark;
                $temp1->amount = $a->service_amount;
                $temp1->status = "unpaid";
                $temp1->group = "Service Charge";
                $temp1->save();
            }


            $w = $a->wardCheck;
            foreach ($w as $key1 => $value1) {
                if ($value1->checkout) {
                    $bill = number_format($value1->ward->price * (strtotime($value1->checkout) - strtotime($value1->checkin)) / (60 * 60), 2);
                    #
                    if ($value1->bill == 0) {
                        $temp1 = new AttendanceBill();
                        $temp1->attendance_id = $a->id;
                        $temp1->name = $value1->ward->name . ' ' . $value1->bed;
                        $temp1->dosage = NULL;
                        $temp1->amount = $bill;
                        $temp1->status = "unpaid";
                        $temp1->group = "Service Charge";
                        $temp1->save();

                        $value1->bill = $temp1->id;
                        $value1->save();
                    }
                    #

                }
            }

            #Clear Current Medical Session Info
            // $ad = new AttendanceMedical();
            // $ad = $ad->where('attendance_id','=',$a->id);
            // $ad = $ad->where('clinical_id','=',$a->getCurrentClinical()->id);
            // $ad = $ad->delete();

            #Clear Old unconfirmed medical session Info
            $ad = new AttendanceMedical();
            $ad = $ad->where('attendance_id', '=', $a->id);
            $ad = $ad->where('clinical_id', '<', $a->getCurrentClinical()->id);
            $ad = $ad->where('checked', '=', NULL);
            $ad = $ad->where('checker_id', '=', NULL);
            $ad = $ad->delete();

            // return $ad;
            // return DB::getQueryLog();

            foreach ($medical as $key => $value) {
                if ($value > 0) {
                    ++$i;
                    $temp = Medical::find($value);
                    $ad = new AttendanceMedical();
                    $ad->attendance_id = $a->id;
                    $ad->medical_id = $value;
                    $ad->clinical_id = $a->getCurrentClinical()->id;
                    $ad->dosage = $dosage[$i];
                    $ad->creator_id = Auth::user()->id;
                    $ad->save();
                }
            }

            #Procedure
            if (count($procedures) > 0) {
                $j = -1;
                foreach ($procedures as $key => $value) {
                    if ($value > 0) {
                        ++$j;
                        $pro = Procedure::find($value);
                        if (!is_null($pro)) {
                            $temp1 = new AttendanceBill();
                            $temp1->attendance_id = $a->id;
                            $temp1->name = $p->name;
                            $temp1->dosage = '';
                            $temp1->amount = $pro->price($a->id);
                            $temp1->status = "unpaid";
                            $temp1->group = "Service Charge";
                            $temp1->save();
                        }
                    }
                }
            }

            $loc = 5;
            if ($i == -1) {
                $loc = 6;
            }

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

            return Redirect::to('dashboard')->with('success', 'Patient Served');
        }
    }

    public function register_sample($a_id, $t_id)
    {
        $a = PatientAttendance::find($a_id);
        if ($a) {
            $s = explode(',', $a->test);
            foreach ($s as $key => $value) {
                if ($t_id == $value) {
                    #
                    $_title = 'Register Sample';
                    $_sub_title = '';
                    $_page_index = 'sample';
                    $data = array(
                        'patient' => $a->patient,
                        'sample' => LabTest::find($t_id),
                    );
                    return view('patient.register_sample', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
                    #
                }
            }
        }
    }

    public function register_sample_action($a_id, $t_id)
    {
        // return request()->all();
        $a = PatientAttendance::find($a_id);
        if ($a) {
            $s = explode(',', $a->test);
            foreach ($s as $key => $value) {
                if ($t_id == $value) {
                    #
                    if (request('button') == 'Skip Test') {
                        $ad = new AttendanceLab();
                        $ad->attendance_id = $a->id;
                        $ad->clinical_id = $a->LastClinical()->id;
                        $ad->creator_id = Auth::user()->id;
                        $ad->updator_id = Auth::user()->id;
                        $ad->test_id = $value;
                        $ad->sample_register = '-';
                        $ad->sample_register_id = Auth::user()->id;

                        $ad->results_register = 'Skipped';
                        // $ad->result_register_id = Auth::user()->id;

                        $ad->result_register_id = Auth::user()->id;
                        $ad->save();

                        return $this->patient_move($a);

                        // return Redirect::to('dashboard')
                        // ->with('success','Patient test have been skipped successfully');

                    } else {

                        $ad = new AttendanceLab();
                        $ad->attendance_id = $a->id;
                        $ad->clinical_id = $a->LastClinical()->id;
                        $ad->creator_id = Auth::user()->id;
                        $ad->updator_id = Auth::user()->id;
                        $ad->test_id = $value;
                        $ad->sample_register = request('sample');
                        $ad->sample_register_id = Auth::user()->id;
                        $ad->save();

                        $ab = new AttendanceBill();
                        $ab->attendance_id = $a->id;
                        $ab->name = LabTest::find($value)->name;
                        $ab->amount = LabTest::find($value)->price($a->id);
                        $ab->status = "unpaid";
                        $ab->group = "Laboratory";
                        $ab->save();

                        return Redirect::to('dashboard')
                            ->with('success', 'Patient sample have been recorded successfully');
                    }

                    #

                }
            }
        }
    }


    public function register_result($id)
    {
        $al = AttendanceLab::find($id);
        $a = PatientAttendance::find($al->attendance_id);
        // return $a;
        // return $al;
        if ($al && $a) {
            $s = explode(',', $a->test);
            foreach ($s as $key => $value) {
                if ($al->test_id == $value) {
                    #
                    $_title = 'Register Result';
                    $_sub_title = 'add';
                    $_page_index = 'sample';
                    $data = array(
                        'patient' => $a->patient,
                        'attend' => $a,
                        'result' => $al,
                        'test' => LabTest::find($al->test_id),
                    );
                    return view('patient.register_result', array_merge($data, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
                    #
                }
            }
        }
    }

    public function register_result_action($id)
    {
        // return request()->all();
        $al = AttendanceLab::find($id);
        $a = PatientAttendance::find($al->attendance_id);
        $p = $a->patient;
        $lc = $a->LastClinical();
        // return $a;
        // return $al;
        if ($al && $a) {
            $s = explode(',', $a->test);
            foreach ($s as $key => $value) {
                if ($al->test_id == $value) {
                    #
                    $al->results_register = request('result');
                    $al->result_register_id = Auth::user()->id;

                    $file = request()->file('attachment');
                    if ($file) {
                        $path = 'uploads/';
                        $filename = uniqid(date('Hmdysi')) . '_' . $file->getClientOriginalName();
                        $upload = $file->move($path, $filename);
                        if ($upload) {
                            $al->attachment = $path . $filename;
                        }
                    }
                    $al->save();
                    #
                }
            }

            #
            return $this->patient_move($a);
            #
        }
    }


    public function patient_move($a)
    {
        $s = explode(',', $a->test);
        $lc = $a->LastClinical();
        $send = false;
        $p = $a->patient;

        foreach ($s as $key => $value) {
            $al = new AttendanceLab();
            $al = $al->where('attendance_id', '=', $a->id);
            $al = $al->where('clinical_id', '=', $lc->id);
            $al = $al->where('test_id', '=', $value);
            $al = $al->first();
            if (!is_null($al) && $al->result_register_id > 0 && $al->result_register_id != "" && $al->result_register_id != null) {
                $send = true;
            } else {
                $send = false;
                return Redirect::to('dashboard')->with('success', 'Test record saved');
                break;
            }
        }

        if ($send) {
            #Cheeting
            $p->location_office_id = 1;
            $p->location_office_id = $a->doctor->office->id; #Office
            $p->save();

            #Clean Attendance
            $m = AttendanceMovement::clean($a->id);

            #Attendance Movement
            $m = new AttendanceMovement();
            $m->attendance_id = $a->id;
            $m->from_id = Auth::user()->id;
            $m->from_office_id = Auth::user()->office_id;
            $m->to_office_id = 1;
            $m->to_office_id = $a->doctor->office->id; #Office
            $m->notes = '';
            $m->status = 1;
            $m->save();

            return Redirect::to('dashboard')->with('success', 'Patient have been served');
        }
        return Redirect::to('dashboard')->with('success', 'Test record saved');
    }


    public function lab_edit()
    {
        if (Auth::user()->role_id == 4) {
            #Patient New Sample
            $l = DB::table('patient_queue');
            $l = $l->where('status', '=', 1);
            $l->where('to_office_id', '=', Auth::user()->office_id);
            $r = $l->get();

            $data = array();
            foreach ($r as $key => $value) {
                $a_temp = PatientAttendance::find($value->attendance_id);
                $test = explode(',', $a_temp->test);
                foreach ($test as $key1 => $value1) {

                    $c_temp = $a_temp->LastClinical();

                    $al = new AttendanceLab();
                    $al = $al->where('attendance_id', '=', $a_temp->id);
                    $al = $al->where('clinical_id', '=', $c_temp->id);
                    $al = $al->where('test_id', '=', $value1);
                    $al = $al->first();


                    if (is_null($al)) {
                        // $lt = LabTest::find($value1);
                        // $temp = (object)array(
                        // 	'time' => date('H:i D, d M Y',strtotime($value->created_at)),
                        // 	'a_id' => $a_temp->id,
                        // 	't_id' => $value1,
                        // 	'patient' => $value->patient,
                        // 	'gender' => $value->gender,
                        // 	'from_user' => $value->from_user,
                        // 	'test' => $lt->uom.' - '.$lt->name,
                        // );
                        // $data1[] = $temp;
                    } else if ($al->results_register == null || $al->results_register == '') {
                        // $lt = LabTest::find($value1);
                        // $temp = (object)array(
                        // 	'time' => date('H:i D, d M Y',strtotime($al->created_at)),
                        // 	'a_id' => $a_temp->id,
                        // 	'al' => $al,
                        // 	't_id' => $value1,
                        // 	'patient' => $value->patient,
                        // 	'gender' => $value->gender,
                        // 	'from_user' => $value->from_user,
                        // 	'sample' => $al->sample_register,
                        // 	'test' => $lt->uom.' - '.$lt->name,
                        // );
                        // $data2[] = $temp;
                    } else if ($al->results_register == 'Skipped' && $a_temp->updated_at > $al->updated_at) {
                        // return $al;
                        // $al->delete();
                        // $lt = LabTest::find($value1);
                        // $temp = (object)array(
                        // 	'time' => date('H:i D, d M Y',strtotime($value->created_at)),
                        // 	'a_id' => $a_temp->id,
                        // 	't_id' => $value1,
                        // 	'patient' => $value->patient,
                        // 	'gender' => $value->gender,
                        // 	'from_user' => $value->from_user,
                        // 	'test' => $lt->uom.' - '.$lt->name,
                        // );
                        // $data1[] = $temp;
                    } else {
                        $lt = LabTest::find($value1);
                        $temp = (object)array(
                            'time' => date('H:i D, d M Y', strtotime($al->created_at)),
                            'a_id' => $a_temp->id,
                            'al' => $al,
                            't_id' => $value1,
                            'patient' => $value->patient,
                            'gender' => $value->gender,
                            'from_user' => $value->from_user,
                            'sample' => $al->sample_register,
                            'test' => $lt->uom . ' - ' . $lt->name,
                        );
                        $data[] = $temp;
                    }
                }
            }

            $datax['data'] = $data;
        }
        $_title = 'Modify Results';
        $_sub_title = '<i class="icon-home"></i>';
        $_page_index = 'lab_edit';
        return view('patient.lab_edit', array_merge($datax, ['title' => $_title ?? '', 'sub_title' => $_sub_title ?? '', 'page_index' => $_page_index ?? '', 'table' => isset($_layout_table) && isset($data['table']) ? (object)array_merge((array)$_layout_table, (array)$data['table']) : (isset($data['table']) ? $data['table'] : ($_layout_table ?? null))]));
    }
}
