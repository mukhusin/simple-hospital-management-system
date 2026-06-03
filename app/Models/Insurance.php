<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Insurance extends Model {
	protected $fillable = [];

	/**
	 * Returns true when this insurance scheme is NHIF.
	 * Matches by name keyword or by nhif_scheme_id being set.
	 */
	public function isNhif(): bool
	{
		return !empty($this->nhif_scheme_id)
			|| stripos($this->name ?? '', 'nhif') !== false;
	}

	public function prices()
	{
		return $this->hasMany(InsurancePrice::class, 'insurance_id');
	}
}