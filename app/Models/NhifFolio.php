<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhifFolio extends Model
{
    protected $fillable = [
        'attendance_id', 'folio_no', 'patient_card_no', 'authorization_no',
        'claim_year', 'claim_month', 'amount_claimed', 'status',
        'folio_items', 'diseases', 'submitted_at', 'confirmed_at',
        'signed_at', 'rejection_reason', 'creator_id',
    ];

    protected $casts = [
        'folio_items'  => 'array',
        'diseases'     => 'array',
        'submitted_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'signed_at'    => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(PatientAttendance::class, 'attendance_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    public static function forMonth(int $year, int $month)
    {
        return static::where('claim_year', $year)->where('claim_month', $month);
    }
}
