<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Ward extends Model {
	
	protected $fillable = [];
	
	public function creator()
	{
		return $this->belongsTo(User::class,'creator_id');
	}
	
	public function updator()
	{
		return $this->belongsTo(User::class,'updator_id');
	}
	
}