<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AttendancePayment extends Model {
	protected $fillable = [];

	public function attendance()
	{
		return $this->belongsTo(PatientAttendance::class);
	}

	public function creator()
	{
		return $this->belongsTo(User::class,'creator_id');
	}

	public function dispensor()
	{
		return $this->belongsTo(User::class,'dispense_id');
	}


	static function last_list()
	{
		$l = new AttendancePayment();
		$l = $l->orderBy('created_at','desc');
		return $l->limit(100)->get();
	}
	
}