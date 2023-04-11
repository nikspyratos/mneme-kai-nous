<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EskomSePushApiClient
{
    private string $baseUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->baseUrl = config('app.eskomsepush.base_url');
        $this->apiToken = config('app.eskomsepush.api_token');
    }

    public function getZoneData(string $zoneId)
    {
        $response = Http::withHeaders(['Token' => $this->apiToken])
            ->get($this->baseUrl . '/business/2.0/area', ['id' => $zoneId]);
        Log::debug('EskomSePushApiClient getZoneData', ['code' => $response->status(), 'data' => $response->json()]);

        return $response->json();
    }
}
