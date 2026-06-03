<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AttendanceClinical extends Model {
	protected $fillable = [];

	public function attendance()
	{
		return $this->belongsTo(PatientAttendance::class,'attendance_id');
	}


    public function creator()
    {
        return $this->belongsTo(User::class,'creator_id');
    }

	public function labResults()
	{
		return $this->hasMany(AttendanceLab::class,'clinical_id');
	}


	public function medicals()
	{
		return $this->hasMany(AttendanceMedical::class,'clinical_id');
	}

}