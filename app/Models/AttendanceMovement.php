<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AttendanceMovement extends Model {
	protected $fillable = [];

	public function attendance()
	{
		return $this->belongsTo(PatientAttendance::class);
	}

	static function clean($id)
	{

		$a = new AttendanceMovement();
		$a = $a->where('attendance_id','=',$id);
		$a = $a->get();

		foreach ($a as $key => $value) {
			$value->status = 0;
			$value->save();
		}
		
	}
	
}