<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AttendanceBill extends Model {
	protected $fillable = [];

	public function attendance()
	{
		return $this->belongsTo(PatientAttendance::class);
	}

	static function getDoctorBill($attend_id='')
	{
		$a = new AttendanceBill();
		$a = $a->where('attendance_id','=',$attend_id);
		$a = $a->where('name','=','Consultation Fee');
		return $a->first();
	}


	static function last_list()
	{
		$l = new AttendanceBill();
		$l = $l->orderBy('created_at','desc');
		return $l->limit(100)->get();
	}

}