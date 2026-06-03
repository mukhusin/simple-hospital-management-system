<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PaymentBreak extends Model {
	protected $fillable = [];

	public function payment()
	{
		return $this->belongsTo(AttendacePayment::class);
	}

}