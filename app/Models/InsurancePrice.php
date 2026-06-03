<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class InsurancePrice extends Model {
	protected $fillable = [];

	public function insurance()
	{
		return $this->belongsTo(Insurance::class,'insurance_id');
	}

}