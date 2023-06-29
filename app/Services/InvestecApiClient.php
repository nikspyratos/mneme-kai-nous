<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use InvestecSdkPhp\Connectors\InvestecConnector;
use InvestecSdkPhp\DataTransferObjects\PrivateBanking\PayMultiple\PayMultipleDto;
use InvestecSdkPhp\DataTransferObjects\PrivateBanking\TransferMultiple\TransferMultipleDto;
use InvestecSdkPhp\Enumerations\TransactionType;
use Saloon\Contracts\Authenticator;

class InvestecApiClient
{
    private InvestecConnector $client;

    public function __construct()
    {
        $this->client = new InvestecConnector(
            config('app.bank_apis.investec.client_id'),
            config('app.bank_apis.investec.secret'),
            config('app.bank_apis.investec.api_key')
        );
    }

    public function getAccounts(): array
    {
        $response = $this->client->privateBanking($this->getAuthentication())->getAccounts();
        Log::debug('Investec getAccounts', ['code' => $response->status(), 'data' => $response->body()]);

        return $response->json('data.accounts');
    }

    public function getAccountBalance(string $accountIdentifier): array
    {
        $response = $this->client->privateBanking($this->getAuthentication())->getAccountBalance($accountIdentifier);
        Log::debug('Investec getAccountBalance', ['code' => $response->status(), 'data' => $response->body()]);

        return $response->json('data');
    }

    public function getTransactions(string $accountIdentifier, ?string $startDate = null, ?string $endDate = null, ?TransactionType $transactionType = null): array
    {
        $fromDate = $startDate ?? Carbon::today()->subDay()->format('Y-m-d');
        $toDate = $endDate ?? Carbon::today()->addDay()->format('Y-m-d');
        $response = $this->client->privateBanking($this->getAuthentication())->getAccountTransactions($accountIdentifier, $fromDate, $toDate, $transactionType);
        Log::debug('Investec getTransactions', ['account' => $accountIdentifier, 'code' => $response->status(), 'data' => $response->body()]);

        return $response->json('data.transactions');
    }

    public function getBeneficiaries()
    {
        $response = $this->client->privateBanking($this->getAuthentication())->getBeneficiaries();
        Log::debug('Investec getBeneficiaries', ['code' => $response->status(), 'data' => $response->body()]);

        return $response->json('data');
    }

    public function transferMultiple(string $accountIdentifier, TransferMultipleDto $transferMultipleDto)
    {
        $response = $this->client->privateBanking($this->getAuthentication(['transactions']))->transferMultiple($accountIdentifier, $transferMultipleDto);
        Log::debug('Investec transferMultiple', ['account' => $accountIdentifier, 'code' => $response->status(), 'data' => $response->body()]);

        return $response->json('data.TransferResponses');
    }

    public function payMultiple(string $accountIdentifier, PayMultipleDto $payMultipleDto)
    {
        $response = $this->client->privateBanking($this->getAuthentication(['transactions']))->payMultiple($accountIdentifier, $payMultipleDto);
        Log::debug('Investec payMultiple', ['account' => $accountIdentifier, 'code' => $response->status(), 'data' => $response->body()]);

        return $response->json('data.TransferResponses');
    }

    private function getAuthentication(array $scopes = ['accounts']): Authenticator
    {
        return Cache::remember('bank_apis.investec.access_token_' . Arr::join($scopes, '_'), 1799, function () use ($scopes) {
            try {
                return $this->client->getAccessToken($scopes);
            } catch (Exception $e) {
                Log::error('Investec getAccessToken failed', ['message' => $e->getMessage()]);
            }

            return null;
        });
    }
}
