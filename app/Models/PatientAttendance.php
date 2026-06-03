<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PatientAttendance extends Model {
	
	protected $fillable = [];

	public function patient()
	{
		return $this->belongsTo(Patient::class);
	}

	static function getCurrent($patient_id)
	{
		$a = new PatientAttendance();
		$a = $a->where('patient_id','=',$patient_id);
		$a = $a->orderby('created_at','desc');
		return $a->first();
	}

	public function labResults()
	{
		return $this->hasMany(AttendanceLab::class,'attendance_id');
	}


	public function LastClinical()
	{
		$c = new AttendanceClinical();
		$c = $c->where('attendance_id','=',$this->id);
		$c = $c->orderby('created_at','desc');
		return $c->first();
	}

	public function canTest($test_id)
	{

		$t = new AttendanceLab();
		$t = $t->where('attendance_id','=',$this->id);
		$t = $t->where('test_id','=',$test_id);
		$t = $t->where('clinical_id','=',$this->LastClinical()->id);
		// $t = $t->where('results_register','=',NULL);
		$r = $t->first();

		if (!is_null($r) && $r->id) {
			return false;
		}

		return true;

	}

	public function Clinicals()
	{
		return $this->hasMany(AttendanceClinical::class,'attendance_id');
	}

	public function medicals()
	{
		return $this->hasMany(AttendanceMedical::class,'attendance_id');
	}

	public function bill()
	{
		return $this->hasMany(AttendanceBill::class,'attendance_id');
	}

	public function wardCheck()
	{
		return $this->hasMany(WardCheck::class,'attendance_id');
	}

	public function doctor()
	{
		return $this->belongsTo(User::class,'doctor_id');
	}

	public function getCurrentClinical()
	{
		$a = new AttendanceClinical();
		$a = $a->where('attendance_id','=',$this->id);
		$a = $a->orderby('created_at','desc')->first();
		return $a;
	}

	public function listService($type='')
	{
		if ($type == '') {
			$b = DB::table('patient_bills')
				->where('attendance_id','=',$this->id)
				->where('group','=','Service Charge')->get();
			$data = array();
			foreach ($b as $key => $value) {
				if ($value->dosage) {
					$data[] = $value->name.' - '.$value->dosage;
				}
				else {
					$data[] = $value->name.' '.$value->dosage;
				}
			}
			return implode(',', $data);
		}
	}

	public function insurance()
	{
		return $this->belongsTo(Insurance::class,'insurance_id');
	}

    public static function sumGroup($group, $id)
    {
        $va = DB::table('patient_bills')
            // ->select('created_at', 'amount','group')
            ->select( DB::raw('sum(amount) as sum') )
            ->where('attendance_id','=',$id)
            ->where('group','=',$group)
//            ->where(DB::raw('date(created_at)'),'=',$key)
             ->groupBy('group')
//            ->orderby('created_at','desc')
            ->get();
        if( isset($va[0]))
            return $va[0]->sum;
        return 0;
    }

    public function nhifFolio()
    {
        return $this->hasOne(NhifFolio::class, 'attendance_id');
    }

    public function isNhifPatient(): bool
    {
        return $this->insurance_id > 0 && !empty($this->nhif_card_no);
    }

    public function nhifClaimPending(): bool
    {
        return $this->isNhifPatient() && in_array($this->nhif_claim_status, [null, 'pending']);
    }

}