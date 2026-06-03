<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Expense extends Model {

    protected $fillable = [];
    protected $table = 'expenses';

    public function creator()
    {
        return $this->belongsTo(User::class,'creator_id');
    }

    public function updator()
    {
        return $this->belongsTo(User::class,'updator_id');
    }

}