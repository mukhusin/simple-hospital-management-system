<?php

namespace App\Services\Nhif;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class NhifAuthService
{
    private string $authUrl;
    private string $clientId;
    private string $clientSecret;
    private string $scope;

    public function __construct()
    {
        $this->authUrl      = config('services.nhif.auth_url');
        $this->clientId     = config('services.nhif.client_id');
        $this->clientSecret = config('services.nhif.client_secret');
        $this->scope        = config('services.nhif.scope', 'OnlineServices');
    }

    /**
     * Get a valid Bearer token, refreshing from the auth server if expired.
     */
    public function getToken(): string
    {
        $cacheKey = 'nhif_access_token_' . md5($this->clientId);

        return Cache::remember($cacheKey, 3300, function () {
            return $this->fetchToken();
        });
    }

    /**
     * Force-refresh the token (e.g. after a 401 response).
     */
    public function refreshToken(): string
    {
        $cacheKey = 'nhif_access_token_' . md5($this->clientId);
        Cache::forget($cacheKey);
        return $this->getToken();
    }

    private function fetchToken(): string
    {
        $response = Http::asForm()->post($this->authUrl, [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => $this->scope,
        ]);

        if ($response->failed()) {
            $error = $response->json('error_description') ?? $response->body();
            throw new RuntimeException("NHIF auth failed: {$error}");
        }

        $token = $response->json('access_token');

        if (empty($token)) {
            throw new RuntimeException('NHIF auth response did not include an access_token.');
        }

        return $token;
    }
}
