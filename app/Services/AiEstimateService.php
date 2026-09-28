<?php

namespace App\Services;

class AiEstimateService
{
    private array $basePrices = [
        1 => 5000,
        2 => 4000,
        3 => 6000,
        4 => 3000,
        5 => 7000,
        6 => 5000,
    ];

    public function estimate(int $categoryId, string $description, bool $isEmergency = false): array
    {
        $basePrice = $this->basePrices[$categoryId] ?? 5000;
        $bonus     = floor(mb_strlen($description) / 100) * 1000;
        $emergency = $isEmergency ? 2000 : 0;

        $estimate = $basePrice + $bonus + $emergency;

        return [
            'estimate'        => $estimate,
            'base_price'      => $basePrice,
            'bonus'           => $bonus,
            'emergency_bonus' => $emergency,
            'currency'        => 'ريال',
        ];
    }
}
