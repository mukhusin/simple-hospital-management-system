<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Medical extends Model {
	protected $fillable = [];

	static function list1()
	{
		#Return Array of Labtest
		$b = DB::table('mediacals')->select('id','name')->get();
		foreach ($b as $key => $value) {
			$data[] = $value;
		}
		return $data;
	}

	public function stock()
	{
		return $this->hasMany(MedicalStock::class);
	}

	static function last_list()
	{
		$l = new Medical();
		$l = $l->orderBy('created_at','desc');
		return $l->limit(100)->get();
	}

	static function zero()
	{
		$l = new Medical();
		$l = $l->where('stock','=',0);
		$l = $l->orderBy('name');
		return $l->get();
	}

	public function getStock()
	{
		$ms = new MedicalStock();
		$ms = $ms->where('medical_id','=',$this->id);
		$ms = $ms->orderBy('created_at','desc');
		$s = $ms->first();

		if (!is_null($s)) {
			return $s->stock;
		}

		return $this->stock;
	}


	public function price($aid='')
	{
		$a = PatientAttendance::find($aid);
		if (!is_null($a) && $a->insurance_id > 0) {
			$ip = new InsurancePrice();
			$ip = $ip->where('insurance_id','=',$a->insurance_id);
			$ip = $ip->where('type','=','medical');
			$ip = $ip->where('target_id','=',$this->id)->first();

			if (!is_null($ip)) {
				return $ip->price;
			}

		}

		return $this->price_unit;
	}


	public function prices()
	{
		$ip = new InsurancePrice();
		$ip = $ip->where('type','=','medical');
		$ip = $ip->where('target_id','=',$this->id)->get();

		$temp = array();
		foreach ($ip as $key => $value) {
			$temp[] = $value;
		}

		return $temp;
		
	}


}