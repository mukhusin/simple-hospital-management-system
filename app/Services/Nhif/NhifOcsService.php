<?php

namespace App\Services\Nhif;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class NhifOcsService
{
    private string $baseUrl;
    private NhifAuthService $auth;

    public function __construct(NhifAuthService $auth)
    {
        $this->auth    = $auth;
        $this->baseUrl = rtrim(config('services.nhif.base_url'), '/') . '/ocs';
    }

    // -------------------------------------------------------------------------
    // Claims
    // -------------------------------------------------------------------------

    /**
     * Test connectivity to the OCS API.
     */
    public function test(): array
    {
        return $this->get('/api/Claims/Test');
    }

    /**
     * Submit a single patient folio (claim) to NHIF.
     *
     * Folio fields:
     *   FacilityCode, ClaimYear, ClaimMonth, FolioNo, CardNo,
     *   FirstName, LastName, Gender, DateOfBirth, TelephoneNo,
     *   PatientFileNo, BillNo, ClinicalNotes, AuthorizationNo,
     *   AttendanceDate, VisitTypeID, PatientTypeCode,
     *   DateAdmitted, DateDischarged, AttendingPractitioners,
     *   AmountClaimed, MainDiagnosisCode, ConfirmationCode,
     *   FolioDiseases (array of FolioDisease),
     *   FolioItems (array of FolioItem),
     *   Signatures (array of Signature),
     *   CreatedBy
     */
    public function submitFolio(array $folio): array
    {
        return $this->post('/api/Claims/SubmitFolio', $folio);
    }

    /**
     * Sign a folio (patient/biometric signature before final submission).
     *
     * payload: SignatureModel fields:
     *   CardNo, AuthorizationNo, AmountClaimed,
     *   SignatureData, FpCode, SignatureMethod,
     *   SignedBy, SignedByName, Remarks
     */
    public function signFolio(array $payload): array
    {
        return $this->post('/api/Claims/SignFolio', $payload);
    }

    /**
     * Request a bill confirmation code (OTP sent to patient's phone).
     *
     * payload: BillConfirmationRequestModel fields:
     *   FacilityCode, CardNo, AuthorizationNo,
     *   AttendanceDate, TotalAmount
     */
    public function requestBillConfirmation(array $payload): array
    {
        return $this->post('/api/Claims/RequestBillConfirmation', $payload);
    }

    /**
     * Get bill confirmation status.
     */
    public function getBillConfirmation(string $authorizationNo): array
    {
        return $this->get('/api/Claims/GetBillConfirmation', [
            'authorizationNo' => $authorizationNo,
        ]);
    }

    /**
     * Send confirmation code (patient confirms bill via OTP).
     */
    public function sendConfirmationCode(string $authorizationNo, string $code): array
    {
        return $this->get('/api/Claims/SendConfirmationCode', [
            'authorizationNo' => $authorizationNo,
            'code'            => $code,
        ]);
    }

    /**
     * Get all submitted claims for a facility in a given month.
     */
    public function getSubmittedClaims(string $facilityCode, int $year, int $month): array
    {
        return $this->get('/api/Claims/GetSubmittedClaims', [
            'facilityCode' => $facilityCode,
            'claimYear'    => $year,
            'claimMonth'   => $month,
        ]);
    }

    /**
     * Get claim receipt for a specific folio.
     */
    public function getReceipt(string $facilityCode, int $year, int $month, int $folioNo): array
    {
        return $this->get('/api/Claims/GetReceipt', [
            'facilityCode' => $facilityCode,
            'claimYear'    => $year,
            'claimMonth'   => $month,
            'folioNo'      => $folioNo,
        ]);
    }

    /**
     * Submit the monthly claim batch (final submission after all folios are submitted).
     *
     * payload: MonthlySubmissionModel fields:
     *   FacilityCode, ClaimYear, ClaimMonth,
     *   FoliosSubmitted, TotalAmountClaimed, SubmissionRemarks
     */
    public function submitMonthlyClaim(array $payload): array
    {
        return $this->post('/api/Claims/SubmitMonthlyClaim', $payload);
    }

    // -------------------------------------------------------------------------
    // Packages & Pricing
    // -------------------------------------------------------------------------

    /**
     * Get all benefit schemes.
     */
    public function getBenefitSchemes(): array
    {
        return $this->get('/api/Packages/GetBenefitSchemes');
    }

    /**
     * Get services for a specific scheme.
     */
    public function getSchemeServices(string $schemeId): array
    {
        return $this->get('/api/Packages/GetSchemeServices', ['schemeId' => $schemeId]);
    }

    /**
     * Get all products.
     */
    public function getProducts(): array
    {
        return $this->get('/api/Packages/GetProducts');
    }

    /**
     * Get the full price list for the facility's package.
     */
    public function getPriceList(string $pricePackageId): array
    {
        return $this->get('/api/Packages/GetPriceList', [
            'pricePackageId' => $pricePackageId,
        ]);
    }

    /**
     * Get price packages.
     */
    public function getPricePackages(): array
    {
        return $this->get('/api/Packages/GetPricePackages');
    }

    /**
     * Get a specific price package by ID.
     */
    public function getPricePackage(string $pricePackageId): array
    {
        return $this->get('/api/Packages/GetPricePackage', [
            'pricePackageId' => $pricePackageId,
        ]);
    }

    /**
     * Get price package for a scheme.
     */
    public function getPricePackageByScheme(string $schemeId): array
    {
        return $this->get('/api/Packages/GetPricePackageByScheme', [
            'schemeId' => $schemeId,
        ]);
    }

    /**
     * Get all billable items.
     */
    public function getItems(): array
    {
        return $this->get('/api/Packages/GetItems');
    }

    /**
     * Get item type categories.
     */
    public function getItemTypes(): array
    {
        return $this->get('/api/Packages/GetItemTypes');
    }

    /**
     * Get excluded services for the package.
     */
    public function getExcludedServices(string $pricePackageId): array
    {
        return $this->get('/api/Packages/GetExcludedServices', [
            'pricePackageId' => $pricePackageId,
        ]);
    }

    /**
     * Get the co-payment schedule.
     */
    public function getCoPaymentSchedule(): array
    {
        return $this->get('/api/Packages/GetCoPaymentSchedule');
    }

    /**
     * Get the cost-sharing schedule.
     */
    public function getCostSharingSchedule(): array
    {
        return $this->get('/api/Packages/GetCostSharingSchedule');
    }

    // -------------------------------------------------------------------------
    // Reference
    // -------------------------------------------------------------------------

    /**
     * Get ICD-10 disease/diagnosis codes.
     */
    public function getDiseases(): array
    {
        return $this->get('/api/Reference/GetDiseases');
    }

    /**
     * Get all NHIF-contracted facilities.
     */
    public function getFacilities(): array
    {
        return $this->get('/api/Facilities/GetFacilities');
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
            $this->auth->refreshToken();
            throw new RuntimeException("NHIF OCS 401 Unauthorized on {$path}. Token refreshed — please retry.");
        }

        if ($response->failed()) {
            $body    = $response->json() ?? $response->body();
            $message = is_array($body) ? json_encode($body) : $body;
            throw new RuntimeException("NHIF OCS error on {$path}: " . $response->status() . " — {$message}");
        }

        $json = $response->json();
        return is_array($json) ? $json : ['data' => $json];
    }
}
