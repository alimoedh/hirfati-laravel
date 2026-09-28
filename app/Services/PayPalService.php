<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayPalService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('hirfati.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $this->clientId     = config('hirfati.paypal.client_id');
        $this->clientSecret = config('hirfati.paypal.client_secret');
    }

    private function getAccessToken(): ?string
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post("{$this->baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        return $response->successful() ? $response->json('access_token') : null;
    }

    public function createPayment(int $requestId, float $amount, string $currency = 'USD'): ?string
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $response = Http::withToken($token)->post("{$this->baseUrl}/v1/payments/payment", [
            'intent' => 'sale',
            'payer'  => ['payment_method' => 'paypal'],
            'transactions' => [[
                'amount'      => ['total' => number_format($amount, 2, '.', ''), 'currency' => $currency],
                'description' => 'طلب خدمة #' . str_pad($requestId, 4, '0', STR_PAD_LEFT),
            ]],
            'redirect_urls' => [
                'return_url' => config('hirfati.site_url') . '/client/payment-success/' . $requestId,
                'cancel_url' => config('hirfati.site_url') . '/client/payment-cancel',
            ],
        ]);

        if (!$response->successful()) return null;

        foreach ($response->json('links', []) as $link) {
            if (($link['rel'] ?? '') === 'approval_url') {
                return $link['href'];
            }
        }

        return null;
    }
}
