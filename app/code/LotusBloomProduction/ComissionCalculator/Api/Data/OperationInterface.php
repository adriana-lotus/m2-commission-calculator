<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Api\Data;

/**
 * Operation interface.
 * @api
 * @since 1.0.0
 */
interface OperationInterface
{
    /**
     * Get operation date.
     *
     * @return \DateTime
     */
    public function getDate(): \DateTime;

    /**
     * Get user ID.
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * Get user type (private or business).
     *
     * @return string
     */
    public function getUserType(): string;

    /**
     * Get operation type (deposit or withdraw).
     *
     * @return string
     */
    public function getOperationType(): string;

    /**
     * Get operation amount.
     *
     * @return float
     */
    public function getAmount(): float;

    /**
     * Get operation currency.
     *
     * @return string
     */
    public function getCurrency(): string;
}
