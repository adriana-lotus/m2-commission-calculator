<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Model\Calculator;

use LotusBloomProduction\ComissionCalculator\Api\ComissionCalculatorInterface;
use LotusBloomProduction\ComissionCalculator\Api\Data\OperationInterface;
use LotusBloomProduction\ComissionCalculator\Service\CommissionRounder;

/**
 * Abstract calculator class.
 */
abstract class AbstractCalculator implements ComissionCalculatorInterface
{
    /**
     * @var CommissionRounder
     */
    protected CommissionRounder $commissionRounder;

    /**
     * @param CommissionRounder $commissionRounder
     */
    public function __construct(
        CommissionRounder $commissionRounder
    ) {
        $this->commissionRounder = $commissionRounder;
    }

    /**
     * Calculate commission for an operation.
     *
     * @param OperationInterface $operation
     * @return float
     */
    public function calculate(OperationInterface $operation): float
    {
        $commission = $this->calculateCommission($operation);
        return $this->commissionRounder->roundUp($commission, $operation->getCurrency());
    }

    /**
     * Calculate raw commission amount before rounding.
     *
     * @param OperationInterface $operation
     * @return float
     */
    abstract protected function calculateCommission(OperationInterface $operation): float;
}
