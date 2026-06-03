<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class WardCheck extends Model {
	
	protected $fillable = [];
	
	public function ward()
	{
		return $this->belongsTo(Ward::class,'ward_id');
	}

	public function creator()
	{
		return $this->belongsTo(User::class,'creator_id');
	}
	
	public function updator()
	{
		return $this->belongsTo(User::class,'updator_id');
	}

	static function clean($id)
	{

		$a = new WardCheck();
		$a = $a->where('attendance_id','=',$id);
		$a = $a->where('checkout','=',NULL);
		$a = $a->get();

		foreach ($a as $key => $value) {
			$value->checkout = Date('Y-m-d H:i:s');
			$value->save();
		}
		
	}
	
}