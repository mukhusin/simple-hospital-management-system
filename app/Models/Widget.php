<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Widget extends Model {
	protected $fillable = [];

	protected $table = 'users';


	static function offices($temp='')
	{
		$data = array();
		if ($temp == 1) {
			$data[''] = 'Select';
			$o = DB::table('offices')
				->where('id','!=',Auth::user()->office_id)
				->where('role_id','=',3)
				->where('hide','=',0)->get();
			foreach ($o as $key => $value) {
				$data[$value->id] = $value->name;
			}
			return $data;
		}
		if ($temp == '') {
			$data[''] = 'Select';
			$o = DB::table('offices')
				->where('id','!=',Auth::user()->office_id)
				->where('hide','=',0)->get();
			foreach ($o as $key => $value) {
				$data[$value->id] = $value->name;
			}
			return $data;
		}
	}


	static function roles($temp='')
	{
		$data = array();
		if ($temp == 1) {
			$data[''] = 'Select';
			$o = DB::table('roles')
				->where('id','!=',Auth::user()->office_id)
				->where('role_id','=',3)
				->where('hide','=',0)->get();
			foreach ($o as $key => $value) {
				$data[$value->id] = $value->name;
			}
			return $data;
		}
		if ($temp == '') {
			$data[''] = 'Select';
			$o = DB::table('roles')
				->where('id','!=',Auth::user()->office_id)
				->where('hide','=',0)->get();
			foreach ($o as $key => $value) {
				$data[$value->id] = $value->name;
			}
			return $data;
		}
	}


	static function diagnosis_list($temp='')
	{
		$data = array();
		if ($temp == '') {
			// $data[''] = 'Select';
			$o = DB::table('diagnosis')->get();
			foreach ($o as $key => $value) {
				$data[$value->id] = $value->code.' - '.$value->name;
			}
			return $data;
		}
	}


	static function paymentMethod($in)
	{
		$data = array();
		$data[''] = 'Select';
		$data['cash'] = 'Cash';
		$data[$in->id] = $in->name;
		return $data;
	}

	static function getInsurance()
	{
		$data = array();
		$data[''] = 'Select';
		$o = DB::table('insurances')->get();
		foreach ($o as $key => $value) {
			$data[$value->id] = $value->name;
		}
		return $data;
	}

	static function getCountries()
	{
		$data = array();
		$data[''] = 'Select';
		$o = DB::table('country')->get();
		foreach ($o as $key => $value) {
			$data[$value->name] = $value->name;
		}
		return $data;
	}


	static function get_diagnosis_array($str)
	{
		$data = array();
		$str = explode(',', $str);
		$o = DB::table('diagnosis')
			->whereIn('id',$str)
			->get();
		foreach ($o as $key => $value) {
			$data[$value->id] = $value->name;
			// $data[$value->id] = $value->code.' - '.$value->name;
		}
		return $data;

	}



	static function test_list($temp='',$insurance_id = null)

	{
		$data = array();
		if ($temp == '') {
			$o = DB::table('lab_tests')->get();
			foreach ($o as $key => $value) {
                if($insurance_id > 0) {
                    $price = DB::table('insurance_prices')->where('target_id','=',$value->id)->where('type','=','lab')->where('insurance_id','=',$insurance_id)->first()->price;
                }
                else {
                    $price = $value->price;
                }
				$data[$value->id] = $value->uom.' - '.$value->name.' - Tshs. '.$price;
			}
			return $data;
		}
	}


	static function meds_list($temp='', $insurance_id = null)
	{
		$data = array();
		$data[] = "Select";
		if ($temp == '') {
			$o = DB::table('medicals')->where('stock','>',0)->get();
			foreach ($o as $key => $value) {
                if($insurance_id > 0) {
                    $price = DB::table('insurance_prices')->where('target_id','=',$value->id)->where('type','=','medical')->where('insurance_id','=',$insurance_id)->first()->price;
                }
                else {
                    $price = $value->price_unit;
                }
				$data[$value->id] = $value->name.' - '.$value->brand.' ('.$value->stock.')'.' - @ Tshs '.$price;
			}
			return $data;
		}
	}

	static function procedures_list($temp='')
	{
		$data = array();
		$data[] = "Select";
		if ($temp == '') {
			$o = DB::table('procedures')->get();
			foreach ($o as $key => $value) {
				$data[$value->id] = $value->name.' - @ Tshs '.$value->price;
			}
			return $data;
		}
	}

	static function refreshWidgets()
	{
		$m = Medical::all();
		foreach ($m as $key => $value) {
			$value->name = ucwords($value->name);
			$value->brand = ucwords($value->brand);
			$value->save();
		}

		$m = LabTest::all();
		foreach ($m as $key => $value) {
			$value->name = ucwords($value->name);
			$value->save();
		}

		$m = Patient::all();
		foreach ($m as $key => $value) {
			$value->name = ucwords($value->name);
			$value->save();
		}

		$m = User::all();
		foreach ($m as $key => $value) {
			$value->name = ucwords($value->name);
			$value->save();
		}

	}


	static function wards($temp='')
	{
		$data = array();
		if ($temp == '') {
			$data[''] = 'Select';
			$o = DB::table('wards')
				->where('hide','=',0)->get();
			foreach ($o as $key => $value) {
				$data[$value->id] = $value->name;
			}
			return $data;
		}
		
	}
	

}