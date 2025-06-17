<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Service;

/**
 * Commission rounder service.
 */
class CommissionRounder
{
    /**
     * Currency decimal places.
     */
    private const CURRENCY_DECIMALS = [
        'EUR' => 2,
        'USD' => 2,
        'JPY' => 0,
        // Add more currencies as needed
    ];

    /**
     * Default decimal places if currency is not defined.
     */
    private const DEFAULT_DECIMALS = 2;

    /**
     * Round up commission amount to currency's decimal places.
     *
     * @param float $amount
     * @param string $currency
     * @return float
     */
    public function roundUp(float $amount, string $currency): float
    {
        $decimals = $this->getDecimalPlaces($currency);
        $multiplier = pow(10, $decimals);

        // Round up to the specified number of decimal places
        return ceil($amount * $multiplier) / $multiplier;
    }

    /**
     * Get decimal places for a currency.
     *
     * @param string $currency
     * @return int
     */
    private function getDecimalPlaces(string $currency): int
    {
        return self::CURRENCY_DECIMALS[$currency] ?? self::DEFAULT_DECIMALS;
    }
}
