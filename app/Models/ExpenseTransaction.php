<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ExpenseTransaction extends Model {

    protected $fillable = [];

    protected $table = 'expense_transactions';

    public function creator()
    {
        return $this->belongsTo(User::class,'creator_id');
    }

    public function updator()
    {
        return $this->belongsTo(User::class,'updator_id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class,'expense_id');
    }

}