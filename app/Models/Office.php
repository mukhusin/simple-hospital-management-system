<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Office extends Model {
	protected $fillable = [];

	const specialist_id = 1;
	const nonspecialist_id = 2;

}