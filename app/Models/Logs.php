<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Logs extends Model {

    protected $fillable = [];

    protected $table = 'logs';

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

    public function insurance()
    {
        return $this->belongsTo(Insurance::class,'insurance_id');
    }

    public function insurancePrice()
    {
        return $this->belongsTo(InsurancePrice::class,'expense_id');
    }

    public function patientAttendance()
    {
        return $this->belongsTo(PatientAttendance::class,'expense_id');
    }


}