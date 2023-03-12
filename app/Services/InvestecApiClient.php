<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InvestecApiClient
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private string $apiKey;
    public function __construct()
    {
        $this->baseUrl = config('app.bank_apis.investec.base_url');
        $this->clientId = config('app.bank_apis.investec.client_id');
        $this->clientSecret = config('app.bank_apis.investec.secret');
        $this->apiKey = config('app.bank_apis.investec.api_key');
    }

    public function getAccounts(): array
    {
        $accessToken = $this->getAccessToken();
        $response = Http::withToken($accessToken)
            ->get($this->baseUrl . '/za/pb/v1/accounts');
        Log::debug('Investec getAccounts', ['code' => $response->status(), 'data' => $response->json()]);
        return $response->json('data.accounts');
    }

    public function getAccountBalance(string $accountIdentifier): array
    {
        $accessToken = $this->getAccessToken();
        $url = $this->baseUrl . sprintf('/za/pb/v1/accounts/%s/balance', $accountIdentifier);
        $response = Http::withToken($accessToken)
            ->get($url);
        Log::debug('Investec getAccountBalance', ['url' => $url, 'code' => $response->status(), 'data' => $response->json()]);
        return $response->json('data');
    }

    public function getTransactions(string $accountIdentifier, ?string $startDate = null, ?string $endDate = null, ?string $transactionType = null): array
    {
        $accessToken = $this->getAccessToken('transactions');
        $data = [
            'fromDate' => $startDate ?? Carbon::today()->subDay()->format('Y-m-d'),
            'toDate' => $endDate ?? Carbon::today()->addDay()->format('Y-m-d')
        ];

        if ($transactionType) {
            $data['transactionType'] = $transactionType;
        }
        $url = $this->baseUrl . sprintf('/za/pb/v1/accounts/%s/transactions', $accountIdentifier);
        $response = Http::withToken($accessToken)
            ->get($url, $data);
        Log::debug('Investec getTransactions', ['account' => $accountIdentifier, 'url' => $url, 'code' => $response->status(), 'data' => $response->json()]);
        return $response->json('data.transactions');
    }

    private function getAccessToken(string $scope = 'accounts'): string
    {
        return Cache::remember('bank_apis.investec.access_token_' . $scope, 1799, function() use ($scope) {
            try {
                $response = Http::asForm()
                    ->withToken(base64_encode($this->clientId . ':' . $this->clientSecret), 'Basic')
                    ->withHeaders(['x-api-key' => $this->apiKey])
                    ->post(
                        $this->baseUrl . '/identity/v2/oauth2/token',
                        [
                            'grant_type' => 'client_credentials',
                            'scope' => $scope
                        ]
                    );
                Log::debug('Investec getAccessToken', ['code' => $response->status(), 'data' => $response->json()]);
                return $response->json('access_token');
            } catch (\Exception $e) {
                Log::error('Investec getAccessToken failed', ['message' => $e->getMessage()]);
            }
            return null;
        });
    }
}
