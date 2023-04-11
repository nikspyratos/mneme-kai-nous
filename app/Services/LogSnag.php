<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogSnag
{
    private string $baseUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->baseUrl = config('app.logsnag.base_url');
        $this->apiToken = config('app.logsnag.api_token');
    }

    public function log(string $eventName, string $description, bool $notify = false, ?array $tags = null, string $icon = '🛸', string $channel = 'default')
    {
        $data = [
            'project' => 'mneme-kai-nous',
            'channel' => $channel,
            'event' => $eventName,
            'description' => $description,
            'icon' => $icon,
            'notify' => $notify,
            'parser' => 'markdown',
        ];
        if ($tags) {
            $data['tags'] = $tags;
        }
        $response = Http::withToken($this->apiToken)
            ->post($this->baseUrl . '/v1/log', $data);
        Log::debug('LogSnag log', ['code' => $response->status(), 'data' => $response->json()]);

        return $response->json();
    }
}
