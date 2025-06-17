<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Model\Calculator;

use LotusBloomProduction\ComissionCalculator\Api\Data\OperationInterface;
use LotusBloomProduction\ComissionCalculator\Service\CommissionRounder;

/**
 * Business withdraw calculator class.
 */
class BusinessWithdrawCalculator extends AbstractCalculator
{
    /**
     * Commission rate for business withdrawals (0.5%).
     */
    private const COMMISSION_RATE = 0.005;

    /**
     * Calculate raw commission amount for business withdraw operations.
     *
     * @param OperationInterface $operation
     * @return float
     */
    protected function calculateCommission(OperationInterface $operation): float
    {
        return $operation->getAmount() * self::COMMISSION_RATE;
    }
}
