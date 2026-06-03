<?php

namespace App\Services\Nhif;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class NhifServiceHubService
{
    private string $baseUrl;
    private NhifAuthService $auth;

    public function __construct(NhifAuthService $auth)
    {
        $this->auth    = $auth;
        $this->baseUrl = rtrim(config('services.nhif.base_url'), '/') . '/servicehub';
    }

    // -------------------------------------------------------------------------
    // Verification
    // -------------------------------------------------------------------------

    /**
     * Verify a member card and get authorization details.
     * Returns the authorization object from NHIF.
     */
    public function verifyCard(array $payload): array
    {
        // payload: { cardNo, pointOfCareID, authorizationNo?, practitionerNo?,
        //            biometricMethod?, fpCode?, imageData? }
        return $this->post('/api/Verification/VerifyCard', $payload);
    }

    /**
     * Get card details by card number.
     */
    public function getCardDetails(string $cardNo): array
    {
        return $this->get('/api/Verification/GetCardDetails', ['cardNo' => $cardNo]);
    }

    /**
     * Get card details by National ID (NIN).
     */
    public function getCardDetailsByNin(string $nin): array
    {
        return $this->get('/api/Verification/GetCardDetailsByNIN', ['nin' => $nin]);
    }

    /**
     * Get beneficiary (member + dependents) details.
     */
    public function getBeneficiaryDetails(string $cardNo, string $cardTypeId, string $verifierId): array
    {
        return $this->post('/api/Verification/GetBeneficiaryDetails', [
            'cardNo'     => $cardNo,
            'cardTypeID' => $cardTypeId,
            'verifierID' => $verifierId,
        ]);
    }

    /**
     * Get authorization details by authorization number.
     */
    public function getAuthorizationDetails(string $authorizationNo): array
    {
        return $this->get('/api/Verification/GetAuthorizationDetails', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Get authorization details by authorization number and date.
     */
    public function getAuthorizationDetailsByDate(string $authorizationNo, string $date): array
    {
        return $this->get('/api/Verification/GetAuthorizationDetailsByDate', [
            'authorizationNo' => $authorizationNo,
            'date'            => $date,
        ]);
    }

    /**
     * Get patient details (for already verified patients).
     */
    public function getPatientDetails(string $authorizationNo): array
    {
        return $this->get('/api/Verification/GetPatientDetails', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Get percentage covered by insurance for the patient.
     */
    public function getPercentCovered(string $authorizationNo): array
    {
        return $this->get('/api/Verification/GetPercentCovered', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Get services excluded from the patient's coverage.
     */
    public function getPatientExcludedServices(string $authorizationNo): array
    {
        return $this->get('/api/Verification/GetPatientExcludedServices', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Get member picture (base64 encoded).
     */
    public function getMemberPicture(string $cardNo): array
    {
        return $this->get('/api/Verification/GetMemberPicture', ['cardNo' => $cardNo]);
    }

    /**
     * Get available visit types.
     */
    public function getVisitTypes(): array
    {
        return $this->get('/api/Verification/GetVisitTypes');
    }

    /**
     * Get points of care for the facility.
     */
    public function getPointsOfCare(): array
    {
        return $this->get('/api/Verification/GetPointsOfCare');
    }

    /**
     * Get card verifier options.
     */
    public function getCardVerifiers(): array
    {
        return $this->get('/api/Verification/GetCardVerifiers');
    }

    /**
     * Generate a Point-of-Care reference number.
     */
    public function generatePocReferenceNo(array $payload): array
    {
        // payload: { pointOfCareID, authorizationNo, practitionerNo, biometricMethod?, fpCode?, imageData? }
        return $this->post('/api/Verification/GeneratePOCReferenceNo', $payload);
    }

    /**
     * Get authorization details by OTP token.
     */
    public function getAuthorizationByToken(string $token): array
    {
        return $this->get('/api/Verification/GetAuthorizationByToken', ['token' => $token]);
    }

    /**
     * Get previous patient visits.
     */
    public function getPreviousPatientVisits(string $cardNo): array
    {
        return $this->get('/api/Verification/GetPreviousPatientVisists', ['cardNo' => $cardNo]);
    }

    // -------------------------------------------------------------------------
    // Approvals
    // -------------------------------------------------------------------------

    /**
     * Request a service approval.
     * payload: ServiceAuthorizationRequestModel
     */
    public function requestApproval(array $payload): array
    {
        return $this->post('/api/Approvals/RequestApproval', $payload);
    }

    /**
     * Update an existing approval request.
     */
    public function updateApprovalRequest(array $payload): array
    {
        return $this->post('/api/Approvals/UpdateApprovalRequest', $payload);
    }

    /**
     * Check patient eligibility for a service.
     */
    public function checkEligibility(string $authorizationNo): array
    {
        return $this->get('/api/Approvals/CheckEligibility', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Get approval status for a given authorization number.
     */
    public function getApprovalStatus(string $authorizationNo): array
    {
        return $this->get('/api/Approvals/GetApprovalStatus', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Get all approvals for the facility.
     */
    public function getApprovals(string $facilityCode): array
    {
        return $this->get('/api/Approvals/GetApprovals', [
            'facilityCode' => $facilityCode,
        ]);
    }

    /**
     * Get approved services for an authorization.
     */
    public function getApprovedServices(string $authorizationNo): array
    {
        return $this->get('/api/Approvals/GetApprovedServices', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Issue (dispense) an approved service to a patient.
     * payload: ServiceIssuanceModel
     */
    public function issueApprovedService(array $payload): array
    {
        return $this->post('/api/Approvals/IssueApprovedService', $payload);
    }

    /**
     * Cancel a previously issued service.
     * payload: IssuanceCancelModel
     */
    public function cancelIssuedService(array $payload): array
    {
        return $this->post('/api/Approvals/IssueCancelService', $payload);
    }

    /**
     * Get the issued approval reference number.
     */
    public function getIssuedApprovalReferenceNo(string $authorizationNo): array
    {
        return $this->get('/api/Approvals/GetIssuedApprovalReferenceNo', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Get approval requests by date range.
     */
    public function getRequestsByDateRange(string $facilityCode, string $from, string $to): array
    {
        return $this->get('/api/Approvals/GetRequestsByDateRange', [
            'facilityCode' => $facilityCode,
            'from'         => $from,
            'to'           => $to,
        ]);
    }

    /**
     * Get allowed services for a scheme.
     */
    public function getAllowedServices(string $schemeId): array
    {
        return $this->get('/api/Approvals/GetAllowedServices', ['schemeId' => $schemeId]);
    }

    /**
     * Get services excluded from an approval.
     */
    public function getApprovalExcludedServices(string $authorizationNo): array
    {
        return $this->get('/api/Approvals/GetExcludedServices', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    // -------------------------------------------------------------------------
    // Admissions
    // -------------------------------------------------------------------------

    /**
     * Admit a patient.
     * payload: PatientAdmissionModel
     */
    public function admitPatient(array $payload): array
    {
        return $this->post('/api/Admissions/AdmitPatient', $payload);
    }

    /**
     * Discharge a patient.
     * payload: PatientDischargeModel
     */
    public function dischargePatient(array $payload): array
    {
        return $this->post('/api/Admissions/DishargePatient', $payload);
    }

    /**
     * Transfer a patient to a different ward/room.
     * payload: PatientTransferModel
     */
    public function transferPatient(array $payload): array
    {
        return $this->post('/api/Admissions/TransferPatient', $payload);
    }

    /**
     * Get all currently admitted patients for the facility.
     */
    public function getAdmittedPatientsByFacility(string $facilityCode): array
    {
        return $this->get('/api/Admissions/GetAdmittedPatientsByFacility', [
            'facilityCode' => $facilityCode,
        ]);
    }

    /**
     * Get admission details by authorization number.
     */
    public function getAdmissionDetailsByAuthorizationNo(string $authorizationNo): array
    {
        return $this->get('/api/Admissions/GetAdmissionDetailsByAuthorizationNo', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Get admission type lookups.
     */
    public function getAdmissionTypes(): array
    {
        return $this->get('/api/Admissions/GetAdmissionTypes');
    }

    /**
     * Get discharge type lookups.
     */
    public function getDischargeTypes(): array
    {
        return $this->get('/api/Admissions/GetDischargeTypes');
    }

    /**
     * Get ward type lookups.
     */
    public function getWardTypes(): array
    {
        return $this->get('/api/Admissions/GetWardTypes');
    }

    /**
     * Get room type lookups.
     */
    public function getRoomTypes(): array
    {
        return $this->get('/api/Admissions/GetRoomTypes');
    }

    /**
     * Send an overstay notification for a long-admitted patient.
     * payload: OverstayNotificationModel
     */
    public function sendOverstayNotification(array $payload): array
    {
        return $this->post('/api/Admissions/SendOverstayNotification', $payload);
    }

    // -------------------------------------------------------------------------
    // Referrals
    // -------------------------------------------------------------------------

    /**
     * Create a service referral to another facility.
     */
    public function createServiceReferral(array $payload): array
    {
        return $this->post('/api/Referrals/CreateServiceReferral', $payload);
    }

    /**
     * Create a treatment referral.
     */
    public function createTreatmentReferral(array $payload): array
    {
        return $this->post('/api/Referrals/CreateTreatmentReferral', $payload);
    }

    /**
     * Acknowledge receipt of a referral.
     */
    public function acknowledgeServiceReferral(array $payload): array
    {
        return $this->post('/api/Referrals/AcknowledgeServiceReferral', $payload);
    }

    /**
     * Get referral by card number.
     */
    public function getReferralByCardNo(string $cardNo): array
    {
        return $this->get('/api/Referrals/GetReferralByCardNo', ['cardNo' => $cardNo]);
    }

    /**
     * Update a referral.
     * payload: PatientReferralUpdateModel
     */
    public function updateReferral(array $payload): array
    {
        return $this->post('/api/Referrals/UpdateReferral', $payload);
    }

    // -------------------------------------------------------------------------
    // Pre-Approvals
    // -------------------------------------------------------------------------

    /**
     * Request pre-approval services (e.g. for surgery or expensive drugs).
     * payload: IssueRequestServiceModel
     */
    public function requestPreApprovalServices(array $payload): array
    {
        return $this->post('/api/PreApprovals/RequestServices', $payload);
    }

    /**
     * Cancel a pre-approval request.
     */
    public function cancelPreApprovalRequest(array $payload): array
    {
        return $this->post('/api/PreApprovals/CancelRequest', $payload);
    }

    /**
     * Issue pre-approved services.
     */
    public function issuePreApprovalServices(array $payload): array
    {
        return $this->post('/api/PreApprovals/IssueRequestServices', $payload);
    }

    // -------------------------------------------------------------------------
    // Practitioner Attendance
    // -------------------------------------------------------------------------

    /**
     * Log in a practitioner (biometric or manual).
     * payload: PractitionerLoginModel
     */
    public function loginPractitioner(array $payload): array
    {
        return $this->post('/api/Attendance/LoginPractitioner', $payload);
    }

    /**
     * Log out a practitioner.
     * payload: PractitionerLoginModel
     */
    public function logoutPractitioner(array $payload): array
    {
        return $this->post('/api/Attendance/LogoutPractitioner', $payload);
    }

    // -------------------------------------------------------------------------
    // Patient History
    // -------------------------------------------------------------------------

    /**
     * Get previous patient visits.
     */
    public function getMedicalHistory(string $cardNo): array
    {
        return $this->get('/api/History/GetMedicalHistory', ['cardNo' => $cardNo]);
    }

    /**
     * Get a visit summary.
     */
    public function getVisitSummary(string $authorizationNo): array
    {
        return $this->get('/api/History/GetVisitSummary', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reference Data
    // -------------------------------------------------------------------------

    /**
     * Get all referral facilities.
     */
    public function getReferralFacilities(): array
    {
        return $this->get('/api/Reference/GetReferralFacilities');
    }

    /**
     * Get all visit types (reference).
     */
    public function getVisitTypesRef(): array
    {
        return $this->get('/api/Reference/GetVisitTypes');
    }

    // -------------------------------------------------------------------------
    // HTTP Helpers
    // -------------------------------------------------------------------------

    private function get(string $path, array $query = []): array
    {
        $response = $this->request()->get($this->baseUrl . $path, $query);
        return $this->handleResponse($response, $path);
    }

    private function post(string $path, array $data): array
    {
        $response = $this->request()->post($this->baseUrl . $path, $data);
        return $this->handleResponse($response, $path);
    }

    private function request()
    {
        return Http::withToken($this->auth->getToken())
                   ->acceptJson()
                   ->timeout(30);
    }

    private function handleResponse(Response $response, string $path): array
    {
        if ($response->status() === 401) {
            // Token may have expired mid-session — refresh once and retry
            $token = $this->auth->refreshToken();
            // Re-run is not done here to avoid recursion; callers should catch 401 if needed
            throw new RuntimeException("NHIF ServiceHub 401 Unauthorized on {$path}. Token refreshed — please retry.");
        }

        if ($response->failed()) {
            $body = $response->json() ?? $response->body();
            $message = is_array($body) ? json_encode($body) : $body;
            throw new RuntimeException("NHIF ServiceHub error on {$path}: " . $response->status() . " — {$message}");
        }

        $json = $response->json();
        return is_array($json) ? $json : ['data' => $json];
    }
}
