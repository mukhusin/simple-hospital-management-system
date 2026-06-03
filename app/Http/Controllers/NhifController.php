<?php

namespace App\Http\Controllers;

use App\Models\AttendanceBill;
use App\Models\NhifFolio;
use App\Models\PatientAttendance;
use App\Services\Nhif\NhifAuthService;
use App\Services\Nhif\NhifOcsService;
use App\Services\Nhif\NhifServiceHubService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class NhifController extends Controller
{
    public function __construct(
        private readonly NhifServiceHubService $hub,
        private readonly NhifOcsService        $ocs,
        private readonly NhifAuthService       $auth,
    ) {}

    // =========================================================================
    // Verification
    // =========================================================================

    /**
     * GET /nhif/verify-card?cardNo=xxx
     * Look up member details and eligibility by card number.
     */
    public function verifyCard(Request $request): JsonResponse
    {
        $request->validate(['cardNo' => 'required|string']);

        return $this->nhifCall(function () use ($request) {
            return $this->hub->verifyCard([
                'cardNo'        => $request->cardNo,
                'pointOfCareID' => $request->input('pointOfCareID', 1),
            ]);
        });
    }

    /**
     * GET /nhif/card-details?cardNo=xxx
     */
    public function getCardDetails(Request $request): JsonResponse
    {
        $request->validate(['cardNo' => 'required|string']);

        return $this->nhifCall(fn () => $this->hub->getCardDetails($request->cardNo));
    }

    /**
     * GET /nhif/card-details-by-nin?nin=xxx
     */
    public function getCardDetailsByNin(Request $request): JsonResponse
    {
        $request->validate(['nin' => 'required|string']);

        return $this->nhifCall(fn () => $this->hub->getCardDetailsByNin($request->nin));
    }

    /**
     * GET /nhif/authorization-details?authorizationNo=xxx
     */
    public function getAuthorizationDetails(Request $request): JsonResponse
    {
        $request->validate(['authorizationNo' => 'required|string']);

        return $this->nhifCall(
            fn () => $this->hub->getAuthorizationDetails($request->authorizationNo)
        );
    }

    /**
     * GET /nhif/percent-covered?authorizationNo=xxx
     */
    public function getPercentCovered(Request $request): JsonResponse
    {
        $request->validate(['authorizationNo' => 'required|string']);

        return $this->nhifCall(
            fn () => $this->hub->getPercentCovered($request->authorizationNo)
        );
    }

    /**
     * GET /nhif/excluded-services?authorizationNo=xxx
     */
    public function getPatientExcludedServices(Request $request): JsonResponse
    {
        $request->validate(['authorizationNo' => 'required|string']);

        return $this->nhifCall(
            fn () => $this->hub->getPatientExcludedServices($request->authorizationNo)
        );
    }

    /**
     * GET /nhif/member-picture?cardNo=xxx
     */
    public function getMemberPicture(Request $request): JsonResponse
    {
        $request->validate(['cardNo' => 'required|string']);

        return $this->nhifCall(fn () => $this->hub->getMemberPicture($request->cardNo));
    }

    /**
     * GET /nhif/visit-types
     */
    public function getVisitTypes(): JsonResponse
    {
        return $this->nhifCall(fn () => $this->hub->getVisitTypes());
    }

    /**
     * GET /nhif/points-of-care
     */
    public function getPointsOfCare(): JsonResponse
    {
        return $this->nhifCall(fn () => $this->hub->getPointsOfCare());
    }

    // =========================================================================
    // Approvals
    // =========================================================================

    /**
     * POST /nhif/request-approval
     * Request a service authorization from NHIF.
     */
    public function requestApproval(Request $request): JsonResponse
    {
        $data = $request->validate([
            'authorizationNo'          => 'required|string',
            'facilityPatientFileNumber'=> 'nullable|string',
            'attendanceDate'           => 'required|date',
            'serviceDate'              => 'nullable|date',
            'clinicalNotes'            => 'nullable|string',
            'practitionerNo'           => 'nullable|string',
            'requestedBy'              => 'nullable|string',
            'createdBy'                => 'nullable|string',
            'approvalDiseases'         => 'nullable|array',
            'authorizedItems'          => 'nullable|array',
        ]);

        return $this->nhifCall(fn () => $this->hub->requestApproval($data));
    }

    /**
     * GET /nhif/approval-status?authorizationNo=xxx
     */
    public function getApprovalStatus(Request $request): JsonResponse
    {
        $request->validate(['authorizationNo' => 'required|string']);

        return $this->nhifCall(
            fn () => $this->hub->getApprovalStatus($request->authorizationNo)
        );
    }

    /**
     * POST /nhif/issue-service
     * Issue/dispense an approved service to a patient.
     */
    public function issueApprovedService(Request $request): JsonResponse
    {
        $data = $request->validate([
            'approvalReferenceNo'  => 'required|string',
            'isBiometricVerified'  => 'boolean',
            'quantity'             => 'required|integer|min:1',
            'description'          => 'nullable|string',
        ]);

        return $this->nhifCall(fn () => $this->hub->issueApprovedService($data));
    }

    // =========================================================================
    // Admissions
    // =========================================================================

    /**
     * POST /nhif/admit-patient
     */
    public function admitPatient(Request $request): JsonResponse
    {
        $data = $request->validate([
            'authorizationNo'      => 'required|string',
            'fullName'             => 'required|string',
            'gender'               => 'required|string|in:M,F',
            'dateOfBirth'          => 'required|date',
            'admissionTypeID'      => 'required|integer',
            'wardTypeID'           => 'required|integer',
            'roomTypeID'           => 'required|integer',
            'chargesPerDay'        => 'required|numeric|min:0',
            'practitionerNo'       => 'nullable|string',
            'diagnosisAtAdmission' => 'nullable|string',
            'practitionersRemarks' => 'nullable|string',
            'dateAdmitted'         => 'required|date',
            'createdBy'            => 'nullable|string',
        ]);

        return $this->nhifCall(fn () => $this->hub->admitPatient($data));
    }

    /**
     * POST /nhif/discharge-patient
     */
    public function dischargePatient(Request $request): JsonResponse
    {
        $data = $request->validate([
            'admissionNo'          => 'required|string',
            'practitionerNo'       => 'nullable|string',
            'practitionersRemarks' => 'nullable|string',
            'dischargeTypeID'      => 'required|integer',
            'dateDischarged'       => 'required|date',
            'diagnosisAtDischarge' => 'nullable|string',
            'referredToFacilityCode' => 'nullable|string',
            'createdBy'            => 'nullable|string',
        ]);

        return $this->nhifCall(fn () => $this->hub->dischargePatient($data));
    }

    /**
     * GET /nhif/admitted-patients?facilityCode=xxx
     */
    public function getAdmittedPatients(Request $request): JsonResponse
    {
        $facilityCode = $request->input(
            'facilityCode',
            config('services.nhif.facility_code')
        );

        return $this->nhifCall(fn () => $this->hub->getAdmittedPatientsByFacility($facilityCode));
    }

    /**
     * GET /nhif/admission-types
     */
    public function getAdmissionTypes(): JsonResponse
    {
        return $this->nhifCall(fn () => $this->hub->getAdmissionTypes());
    }

    // =========================================================================
    // Referrals
    // =========================================================================

    /**
     * POST /nhif/create-referral
     */
    public function createReferral(Request $request): JsonResponse
    {
        $data = $request->validate([
            'authorizationNo' => 'required|string',
            'toFacilityCode'  => 'required|string',
            'practitionerNo'  => 'nullable|string',
            'createdBy'       => 'nullable|string',
            'services'        => 'nullable|array',
            'diseases'        => 'nullable|array',
        ]);

        return $this->nhifCall(fn () => $this->hub->createServiceReferral($data));
    }

    // =========================================================================
    // Claims (OCS)
    // =========================================================================

    /**
     * POST /nhif/submit-folio
     * Submit a single patient claim folio.
     */
    public function submitFolio(Request $request): JsonResponse
    {
        $data = $request->validate([
            'FacilityCode'     => 'required|string',
            'ClaimYear'        => 'required|integer',
            'ClaimMonth'       => 'required|integer|between:1,12',
            'FolioNo'          => 'required|integer',
            'CardNo'           => 'required|string',
            'FirstName'        => 'required|string',
            'LastName'         => 'required|string',
            'Gender'           => 'required|string|in:M,F',
            'DateOfBirth'      => 'required|date',
            'AttendanceDate'   => 'required|date',
            'VisitTypeID'      => 'required|integer',
            'PatientTypeCode'  => 'nullable|string',
            'AuthorizationNo'  => 'nullable|string',
            'AmountClaimed'    => 'required|numeric|min:0',
            'MainDiagnosisCode'=> 'required|string',
            'ConfirmationCode' => 'nullable|string',
            'ClinicalNotes'    => 'nullable|string',
            'PatientFileNo'    => 'nullable|string',
            'BillNo'           => 'nullable|string',
            'FolioDiseases'    => 'nullable|array',
            'FolioItems'       => 'required|array|min:1',
            'FolioItems.*.ItemCode'      => 'required|string',
            'FolioItems.*.ItemTypeID'    => 'required|integer',
            'FolioItems.*.UnitPrice'     => 'required|numeric|min:0',
            'FolioItems.*.ItemQuantity'  => 'required|integer|min:1',
            'FolioItems.*.AmountClaimed' => 'required|numeric|min:0',
            'CreatedBy'        => 'nullable|string',
        ]);

        return $this->nhifCall(fn () => $this->ocs->submitFolio($data));
    }

    /**
     * POST /nhif/sign-folio
     * Sign a folio before submission (patient acknowledgment).
     */
    public function signFolio(Request $request): JsonResponse
    {
        $data = $request->validate([
            'CardNo'          => 'nullable|string',
            'AuthorizationNo' => 'required|string',
            'AmountClaimed'   => 'required|numeric|min:0',
            'SignatureData'   => 'nullable|string',
            'SignatureMethod'  => 'nullable|string',
            'SignedBy'        => 'nullable|string',
            'SignedByName'    => 'nullable|string',
            'Remarks'         => 'nullable|string',
        ]);

        return $this->nhifCall(fn () => $this->ocs->signFolio($data));
    }

    /**
     * POST /nhif/request-bill-confirmation
     * Trigger OTP to patient's phone for bill confirmation.
     */
    public function requestBillConfirmation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'FacilityCode'    => 'required|string',
            'CardNo'          => 'required|string',
            'AuthorizationNo' => 'required|string',
            'AttendanceDate'  => 'required|date',
            'TotalAmount'     => 'required|numeric|min:0',
        ]);

        return $this->nhifCall(fn () => $this->ocs->requestBillConfirmation($data));
    }

    /**
     * GET /nhif/submitted-claims?facilityCode=xxx&year=2025&month=5
     */
    public function getSubmittedClaims(Request $request): JsonResponse
    {
        $request->validate([
            'year'  => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        $facilityCode = $request->input(
            'facilityCode',
            config('services.nhif.facility_code')
        );

        return $this->nhifCall(fn () => $this->ocs->getSubmittedClaims(
            $facilityCode,
            (int) $request->year,
            (int) $request->month
        ));
    }

    /**
     * POST /nhif/submit-monthly-claim
     * Final monthly batch submission.
     */
    public function submitMonthlyClaim(Request $request): JsonResponse
    {
        $data = $request->validate([
            'FacilityCode'       => 'required|string',
            'ClaimYear'          => 'required|integer',
            'ClaimMonth'         => 'required|integer|between:1,12',
            'FoliosSubmitted'    => 'required|integer|min:1',
            'TotalAmountClaimed' => 'required|numeric|min:0',
            'SubmissionRemarks'  => 'nullable|string',
        ]);

        return $this->nhifCall(fn () => $this->ocs->submitMonthlyClaim($data));
    }

    // =========================================================================
    // Packages & Reference Data
    // =========================================================================

    /**
     * GET /nhif/price-list?pricePackageId=xxx
     */
    public function getPriceList(Request $request): JsonResponse
    {
        $request->validate(['pricePackageId' => 'required|string']);

        return $this->nhifCall(
            fn () => $this->ocs->getPriceList($request->pricePackageId)
        );
    }

    /**
     * GET /nhif/benefit-schemes
     */
    public function getBenefitSchemes(): JsonResponse
    {
        return $this->nhifCall(fn () => $this->ocs->getBenefitSchemes());
    }

    /**
     * GET /nhif/diseases — ICD-10 diagnosis codes
     */
    public function getDiseases(): JsonResponse
    {
        return $this->nhifCall(fn () => $this->ocs->getDiseases());
    }

    /**
     * GET /nhif/items — Billable service items
     */
    public function getItems(): JsonResponse
    {
        return $this->nhifCall(fn () => $this->ocs->getItems());
    }

    /**
     * GET /nhif/co-payment-schedule
     */
    public function getCoPaymentSchedule(): JsonResponse
    {
        return $this->nhifCall(fn () => $this->ocs->getCoPaymentSchedule());
    }

    // =========================================================================
    // Practitioner Attendance
    // =========================================================================

    /**
     * POST /nhif/practitioner-login
     */
    public function practitionerLogin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'practitionerNo'  => 'required|string',
            'biometricMethod' => 'nullable|string',
            'fpCode'          => 'nullable|string',
            'imageData'       => 'nullable|string',
        ]);

        return $this->nhifCall(fn () => $this->hub->loginPractitioner($data));
    }

    /**
     * POST /nhif/practitioner-logout
     */
    public function practitionerLogout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'practitionerNo' => 'required|string',
        ]);

        return $this->nhifCall(fn () => $this->hub->logoutPractitioner($data));
    }

    // =========================================================================
    // History
    // =========================================================================

    /**
     * GET /nhif/medical-history?cardNo=xxx
     */
    public function getMedicalHistory(Request $request): JsonResponse
    {
        $request->validate(['cardNo' => 'required|string']);

        return $this->nhifCall(fn () => $this->hub->getMedicalHistory($request->cardNo));
    }

    /**
     * GET /nhif/visit-summary?authorizationNo=xxx
     */
    public function getVisitSummary(Request $request): JsonResponse
    {
        $request->validate(['authorizationNo' => 'required|string']);

        return $this->nhifCall(
            fn () => $this->hub->getVisitSummary($request->authorizationNo)
        );
    }

    // =========================================================================
    // Smart Attendance-Based Actions (used by Blade AJAX)
    // =========================================================================

    /**
     * GET /nhif/card-details?card_no=xxx
     * Frontend-friendly alias: accepts snake_case `card_no`.
     */
    public function getCardDetailsFrontend(Request $request): JsonResponse
    {
        $request->validate(['card_no' => 'required|string']);
        return $this->nhifCall(fn () => $this->hub->getCardDetails($request->card_no));
    }

    /**
     * POST /nhif/attendance/request-otp
     * Send OTP for bill confirmation using attendance_id + card_no.
     */
    public function attendanceRequestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'attendance_id' => 'required|integer',
            'card_no'       => 'required|string',
        ]);

        $attend = PatientAttendance::findOrFail($request->attendance_id);
        $bills  = AttendanceBill::where('attendance_id', $attend->id)
            ->where('status', 'unpaid')->get();
        $total  = $bills->sum('amount');

        $data = [
            'FacilityCode'    => config('services.nhif.facility_code'),
            'CardNo'          => $request->card_no,
            'AuthorizationNo' => $attend->nhif_authorization_no ?? '',
            'AttendanceDate'  => $attend->created_at->format('Y-m-d'),
            'TotalAmount'     => $total,
        ];

        $result = $this->nhifCall(fn () => $this->ocs->requestBillConfirmation($data));

        // Save card number to the attendance record
        $attend->nhif_card_no = $request->card_no;
        $attend->save();

        return $result;
    }

    /**
     * GET /nhif/attendance/get-bill-confirmation
     * Verify OTP code.
     */
    public function attendanceGetBillConfirmation(Request $request): JsonResponse
    {
        $request->validate([
            'card_no'           => 'required|string',
            'confirmation_code' => 'required|string',
        ]);

        return $this->nhifCall(
            fn () => $this->ocs->getBillConfirmation($request->card_no)
        );
    }

    /**
     * POST /nhif/attendance/submit-folio
     * Build and submit a folio from attendance bills.
     */
    public function attendanceSubmitFolio(Request $request): JsonResponse
    {
        $request->validate(['attendance_id' => 'required|integer']);

        $attend  = PatientAttendance::with(['patient', 'insurance'])->findOrFail($request->attendance_id);
        $patient = $attend->patient;
        $bills   = AttendanceBill::where('attendance_id', $attend->id)
            ->where('status', 'unpaid')->get();

        if ($bills->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No unpaid bills found.'], 422);
        }

        $cardNo      = $attend->nhif_card_no ?? $patient->nhif_card_no;
        $claimYear   = (int) $attend->created_at->format('Y');
        $claimMonth  = (int) $attend->created_at->format('n');
        $totalAmount = $bills->sum('amount');
        $facilityCode= config('services.nhif.facility_code');

        // Build FolioItems array from attendance bills
        $folioItems = $bills->map(function ($bill) {
            return [
                'ItemCode'      => $bill->nhif_item_code ?? 'GEN',
                'ItemTypeID'    => $bill->nhif_item_type_id ?? 5,
                'OtherDetails'  => $bill->name,
                'UnitPrice'     => (float) $bill->amount,
                'ItemQuantity'  => 1,
                'AmountClaimed' => (float) $bill->amount,
            ];
        })->values()->toArray();

        $folio = [
            'FacilityCode'     => $facilityCode,
            'ClaimYear'        => $claimYear,
            'ClaimMonth'       => $claimMonth,
            'FolioNo'          => time(), // NHIF may assign; use timestamp as draft
            'CardNo'           => $cardNo,
            'FirstName'        => explode(' ', $patient->name)[0] ?? '',
            'LastName'         => implode(' ', array_slice(explode(' ', $patient->name), 1)) ?: $patient->name,
            'Gender'           => strtoupper(substr($patient->gender ?? 'M', 0, 1)),
            'DateOfBirth'      => $patient->dob ?? '1990-01-01',
            'AttendanceDate'   => $attend->created_at->format('Y-m-d'),
            'VisitTypeID'      => $attend->nhif_visit_type_id ?? 1,
            'AuthorizationNo'  => $attend->nhif_authorization_no ?? '',
            'AmountClaimed'    => $totalAmount,
            'MainDiagnosisCode'=> 'Z00', // Placeholder; doctor should set this
            'PatientFileNo'    => (string) $patient->id,
            'FolioItems'       => $folioItems,
        ];

        $result = $this->ocs->submitFolio($folio);

        // Persist folio record
        $nhifFolio = NhifFolio::firstOrNew(['attendance_id' => $attend->id]);
        $nhifFolio->fill([
            'folio_no'       => $result['FolioNo'] ?? null,
            'patient_card_no'=> $cardNo,
            'authorization_no'=> $attend->nhif_authorization_no,
            'claim_year'     => $claimYear,
            'claim_month'    => $claimMonth,
            'amount_claimed' => $totalAmount,
            'status'         => 'submitted',
            'folio_items'    => $folioItems,
            'submitted_at'   => now(),
            'creator_id'     => Auth::id(),
        ]);
        $nhifFolio->save();

        $attend->nhif_folio_no    = $nhifFolio->folio_no;
        $attend->nhif_claim_status = 'submitted';
        $attend->nhif_claimed_amount = $totalAmount;
        $attend->save();

        return response()->json(['success' => true, 'data' => $result]);
    }

    /**
     * POST /nhif/attendance/sign-folio
     * Sign a submitted folio.
     */
    public function attendanceSignFolio(Request $request): JsonResponse
    {
        $request->validate([
            'attendance_id' => 'required|integer',
            'folio_no'      => 'required|integer',
        ]);

        $attend    = PatientAttendance::findOrFail($request->attendance_id);
        $nhifFolio = NhifFolio::where('attendance_id', $attend->id)->firstOrFail();
        $totalAmount = $nhifFolio->amount_claimed;

        $data = [
            'CardNo'          => $nhifFolio->patient_card_no,
            'AuthorizationNo' => $nhifFolio->authorization_no ?? '',
            'AmountClaimed'   => $totalAmount,
            'SignatureMethod'  => 'Electronic',
            'SignedBy'        => (string) Auth::id(),
            'SignedByName'    => Auth::user()->name ?? 'Staff',
        ];

        $result = $this->ocs->signFolio($data);

        $nhifFolio->status    = 'signed';
        $nhifFolio->signed_at = now();
        $nhifFolio->save();

        $attend->nhif_claim_status = 'signed';
        $attend->save();

        return response()->json(['success' => true, 'data' => $result]);
    }

    /**
     * POST /nhif/attendance/submit-monthly-claim
     * Submit monthly batch for all signed folios in a given period.
     */
    public function attendanceSubmitMonthlyClaim(Request $request): JsonResponse
    {
        $request->validate([
            'claim_year'  => 'required|integer',
            'claim_month' => 'required|integer|between:1,12',
        ]);

        $folios = NhifFolio::forMonth($request->claim_year, $request->claim_month)
            ->where('status', 'signed')->get();

        if ($folios->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No signed folios found for this period.'], 422);
        }

        $data = [
            'FacilityCode'       => config('services.nhif.facility_code'),
            'ClaimYear'          => (int) $request->claim_year,
            'ClaimMonth'         => (int) $request->claim_month,
            'FoliosSubmitted'    => $folios->count(),
            'TotalAmountClaimed' => $folios->sum('amount_claimed'),
        ];

        return $this->nhifCall(fn () => $this->ocs->submitMonthlyClaim($data));
    }

    /**
     * GET /nhif/claims — Claims dashboard view.
     */
    public function claimsDashboard(Request $request)
    {
        $year  = (int) $request->input('year', date('Y'));
        $month = (int) $request->input('month', date('n'));

        $folios = NhifFolio::with(['attendance.patient'])
            ->where('claim_year', $year)
            ->where('claim_month', $month)
            ->orderByDesc('id')
            ->get();

        return view('nhif.claims', compact('folios', 'year', 'month'));
    }

    // =========================================================================
    // Helper
    // =========================================================================

    private function nhifCall(callable $fn): JsonResponse
    {
        try {
            $result = $fn();
            return response()->json(['success' => true, 'data' => $result]);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }
    }
}
