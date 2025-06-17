<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Model\Calculator;

use LotusBloomProduction\ComissionCalculator\Api\Data\OperationInterface;
use LotusBloomProduction\ComissionCalculator\Service\CommissionRounder;

/**
 * Deposit calculator class.
 */
class DepositCalculator extends AbstractCalculator
{
    /**
     * Commission rate for deposits (0.03%).
     */
    private const COMMISSION_RATE = 0.0003;

    /**
     * Calculate raw commission amount for deposit operations.
     *
     * @param OperationInterface $operation
     * @return float
     */
    protected function calculateCommission(OperationInterface $operation): float
    {
        return $operation->getAmount() * self::COMMISSION_RATE;
    }
}
