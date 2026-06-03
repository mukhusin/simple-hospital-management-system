<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class LabTest extends Model {
	protected $fillable = [];

	static function list1()
	{
		#Return Array of Labtest
		// $b = DB::table('lab_tests')->select('id','name')->get();
		// foreach ($b as $key => $value) {
		// 	$data[] = $value;
		// }
		// return $data;

		$b = DB::table('lab_tests')->select('id','name')->get();
		$data[] = 'Select';
		foreach ($b as $key => $value) {
			$data[$value->id] = $value->name;
		}
		return $data;
	}

	static function findName($name='')
	{
		$l = new LabTest();
		$l = $l->where('name','=',$name);
		$l = $l->first();

		if (is_null($l)) {
			return false;
		}

		return true;
	}



 	public function price($aid='')
 	{
 		$a = PatientAttendance::find($aid);
 		if ($a->insurance_id > 0) {
			$ip = new InsurancePrice();
			$ip = $ip->where('insurance_id','=',$a->insurance_id);
			$ip = $ip->where('type','=','lab');
			$ip = $ip->where('target_id','=',$this->id)->first();
			
			if (!is_null($ip)) {
				return $ip->price;
			}

            $price = DB::table('insurance_prices')->where('target_id','=',$this->id)->where('type','=','lab')->where('insurance_id','=',$a->insurance_id)->first()->price;

			return $price;
 
 		}

		return $this->price;
		
 	}


	public function prices()
	{
		$ip = new InsurancePrice();
		$ip = $ip->where('type','=','lab');
		$ip = $ip->where('target_id','=',$this->id)->get();

		$temp = array();
		foreach ($ip as $key => $value) {
			$temp[] = $value;
		}

		return $temp;
		
	}


}
