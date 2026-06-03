<?php

namespace App\Http\Controllers;

use App\Services\Nhif\NhifAuthService;
use App\Services\Nhif\NhifOcsService;
use App\Services\Nhif\NhifServiceHubService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
