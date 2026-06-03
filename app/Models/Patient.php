<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Patient extends Model {
	protected $fillable = [];

	public function is_inoffice()
	{
		if (Auth::user()->office_id == $this->location_office_id) {
			return true;
		}
		return false;
	}

	public function is_paused()
	{
		if ($this->pause_id != null && $this->pause_id > 0 && $this->is_inoffice()) {
			return true;
		}
		return false;
	}

	public function is_new()
	{
		if (($this->location_office_id == 0 || $this->location_office_id == '') && !$this->is_paused()) {
			return true;
		}
		return false;
	}

	public function is_active_inpatient()
	{
		if ($this->location_office_id == 3 && $this->attendance()->ward_served == 1) {
			return true;
		}
		return false;
	}

	public function attendance()
	{
		return PatientAttendance::getCurrent($this->id);
	}

	public function attendances()
	{
		return $this->hasMany(PatientAttendance::class);
	}


	public function attendances_summary()
	{
		$n = new PatientAttendance();
		$n = $n->where('patient_id','=',$this->id);
		$n = $n->orderBy('id','desc');
		return $n->limit(10)->get();
	}

	public function movements()
	{
		return $this->hasMany(AttendanceMovement::class);
	}

	public function vitalSign()
	{
		return $this->hasOne(PatientVital::class);
	}

	public function insurance()
	{
		return $this->belongsTo(Insurance::class,'sponsor');
	}

	public function creator()
	{
		return $this->belongsTo(User::class,'creator_id');
	}



}