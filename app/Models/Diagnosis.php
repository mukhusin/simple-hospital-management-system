<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Diagnosis extends Model {
	protected $fillable = [];
	protected $table = 'diagnosis';
	static function list1()
	{
		#Return Array of Labtest
		$b = DB::table('mediacals')->select('id','name')->get();
		foreach ($b as $key => $value) {
			$data[] = $value;
		}
		return $data;
	}

}