<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;
use App\Traits\HttpResponses;
use Illuminate\Support\Str; 



class CoinGeckoService
{
    use HttpResponses; 

    protected string $baseUrl = 'https://api.coingecko.com/api/v3';

    protected array $currencyMap = [
        'BTC'  => 'bitcoin',
        'ETH'  => 'ethereum',
        'USDT' => 'tether',
    ];

    /**
     * Get NGN rate for a crypto currency
     */
    public function getRate(string $currency): float
    {
        $currency = strtoupper($currency);

        if (!isset($this->currencyMap[$currency])) {
            throw new Exception('Unsupported crypto currency', 400);

        }

        $rates = Cache::remember('crypto_rates_ngn', 60, function () {
            return $this->fetchRates();
        });

        if (!isset($rates[$this->currencyMap[$currency]]['ngn'])) {
            throw new Exception('Rate unavailable', 404);
        }

        return (float) $rates[$this->currencyMap[$currency]]['ngn'];
    }

    /**
     * Fetch rates from CoinGecko
     */
    // protected function fetchRates(): array
    // {
    //     $response = Http::timeout(10)->get("{$this->baseUrl}/simple/price", [
    //         'ids' => implode(',', $this->currencyMap),
    //         'vs_currencies' => 'ngn'
    //     ]);

    //     if (!$response->successful()) {
    //         // throw new Exception("Unable to fetch rates from CoinGecko");
    //         return $this->error([], 'Unable to fetch rates from CoinGecko', 404);
    //     }

    //     return $response->json();
    // }

    protected function fetchRates(): array
    {
        $response = Http::withHeaders([
            'x-cg-demo-api-key' => config('services.coingecko.key'),
        ])
        ->timeout(10)
        ->get("{$this->baseUrl}/simple/price", [
            'ids' => implode(',', $this->currencyMap),
            'vs_currencies' => 'ngn'
        ]);

        if (!$response->successful()) {
            throw new \Exception("Unable to fetch rates from CoinGecko");
        }

        return $response->json();
    }

}
