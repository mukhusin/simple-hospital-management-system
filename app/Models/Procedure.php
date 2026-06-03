<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Procedure extends Model {
	protected $fillable = [];

	public function price($aid='')
	{
		$a = PatientAttendance::find($aid);
		if ($a->insurance_id > 0) {
			$ip = new InsurancePrice();
			$ip = $ip->where('insurance_id','=',$a->insurance_id);
			$ip = $ip->where('type','=','procedure');
			$ip = $ip->where('target_id','=',$this->id)->first();

			if (!is_null($ip)) {
				return $ip->price;
			}

		}

		return $this->price;
		
	}


	public function prices()
	{
		$ip = new InsurancePrice();
		$ip = $ip->where('type','=','procedure');
		$ip = $ip->where('target_id','=',$this->id)->get();

		$temp = array();
		foreach ($ip as $key => $value) {
			$temp[] = $value;
		}

		return $temp;
		
	}

}