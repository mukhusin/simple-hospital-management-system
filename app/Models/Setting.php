<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Setting extends Model {
	protected $fillable = [];


	static function getDoctorRate()
	{
		return Setting::find(1)->doctor_rate;
	}

}