<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class InSession extends Model {
	protected $fillable = [];


	static function record()
	{
		$i = new InSession();
		$i = $i->where('office_id','=',Auth::user()->office_id);
		$i = $i->first();
		$i->user_id = Auth::user()->id;
		$i->changes = uniqid(Auth::user()->id);
		$i->save();

		$d = DB::table("new_insurances_prices")
			->get();
		foreach ($d as $key => $value) {

			foreach (Insurance::all() as $key1 => $value1) {
				$n = new InsurancePrice();
				$n->insurance_id = $value1->id;
				$n->type = $value->type;
				$n->price = $value->price;
				$n->target_id = $value->id;
				$n->save();
			}

		}
	}

	public function office()
	{
		return $this->belongsTo(Office::class);;
	}

	public function user()
	{
		return $this->belongsTo(User::class);;
	}

	static function getUserId($office_id)
	{
		$i = new InSession();
		$i = $i->where('office_id','=',$office_id)->first();
		return $i->user_id;
	}

	static function getAll()
	{
		$i = new InSession();
		return $i->where('hide','=',0)->get();
	}

}