<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Api;

use LotusBloomProduction\ComissionCalculator\Api\Data\OperationInterface;

/**
 * Commission calculator interface.
 * @api
 * @since 1.0.0
 */
interface ComissionCalculatorInterface
{
    /**
     * Calculate commission for an operation.
     *
     * @param OperationInterface $operation
     * @return float The calculated commission amount
     */
    public function calculate(OperationInterface $operation): float;
}
