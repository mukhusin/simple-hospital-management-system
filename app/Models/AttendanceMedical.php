<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AttendanceMedical extends Model {
	protected $fillable = [];

	public function medical()
	{
		$this->belongsTo(Medical::class,'medical_id');
	}
	
}