<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Model\Calculator;

use LotusBloomProduction\ComissionCalculator\Api\Data\OperationInterface;
use LotusBloomProduction\ComissionCalculator\Service\CommissionRounder;
use LotusBloomProduction\ComissionCalculator\Service\CurrencyConverter;

/**
 * Private withdraw calculator class.
 */
class PrivateWithdrawCalculator extends AbstractCalculator
{
    /**
     * Commission rate for private withdrawals (0.3%).
     */
    private const COMMISSION_RATE = 0.003;

    /**
     * Free amount per week in EUR.
     */
    private const FREE_AMOUNT_PER_WEEK = 1000.0;

    /**
     * Maximum free operations per week.
     */
    private const MAX_FREE_OPERATIONS_PER_WEEK = 3;

    /**
     * Base currency for free amount.
     */
    private const BASE_CURRENCY = 'EUR';

    /**
     * @var CurrencyConverter
     */
    private CurrencyConverter $currencyConverter;

    /**
     * @var array
     */
    private array $userWeeklyOperations = [];

    /**
     * @var array
     */
    private array $userWeeklyWithdrawnAmount = [];

    /**
     * @param CommissionRounder $commissionRounder
     * @param CurrencyConverter $currencyConverter
     */
    public function __construct(
        CommissionRounder $commissionRounder,
        CurrencyConverter $currencyConverter
    ) {
        parent::__construct($commissionRounder);
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * Calculate raw commission amount for private withdraw operations.
     *
     * @param OperationInterface $operation
     * @return float
     */
    protected function calculateCommission(OperationInterface $operation): float
    {
        $userId = $operation->getUserId();
        $weekKey = $this->getWeekKey($operation->getDate());
        $userWeekKey = $userId . '_' . $weekKey;

        // Initialize counters for this user and week if not exists
        if (!isset($this->userWeeklyOperations[$userWeekKey])) {
            $this->userWeeklyOperations[$userWeekKey] = 0;
            $this->userWeeklyWithdrawnAmount[$userWeekKey] = 0.0;
        }

        // Increment operation counter for this user and week
        $this->userWeeklyOperations[$userWeekKey]++;

        // Convert operation amount to EUR for free amount calculation
        $amountInEur = $operation->getCurrency() === self::BASE_CURRENCY
            ? $operation->getAmount()
            : $this->currencyConverter->convert(
                $operation->getAmount(),
                $operation->getCurrency(),
                self::BASE_CURRENCY
            );

        // Add to weekly withdrawn amount
        $this->userWeeklyWithdrawnAmount[$userWeekKey] += $amountInEur;

        // Check if this operation is eligible for free amount
        $operationNumber = $this->userWeeklyOperations[$userWeekKey];
        if ($operationNumber <= self::MAX_FREE_OPERATIONS_PER_WEEK) {
            // Calculate how much of this operation is free
            $totalWithdrawnBeforeThisOp = $this->userWeeklyWithdrawnAmount[$userWeekKey] - $amountInEur;
            $freeAmountLeft = max(0, self::FREE_AMOUNT_PER_WEEK - $totalWithdrawnBeforeThisOp);
            $freeAmountForThisOp = min($amountInEur, $freeAmountLeft);

            // Convert free amount back to operation currency
            $freeAmountInOperationCurrency = $operation->getCurrency() === self::BASE_CURRENCY
                ? $freeAmountForThisOp
                : $this->currencyConverter->convert(
                    $freeAmountForThisOp,
                    self::BASE_CURRENCY,
                    $operation->getCurrency()
                );

            // Calculate commission only on the amount exceeding free amount
            $chargeableAmount = max(0, $operation->getAmount() - $freeAmountInOperationCurrency);
            return $chargeableAmount * self::COMMISSION_RATE;
        }

        // For operations beyond the free limit, charge full commission
        return $operation->getAmount() * self::COMMISSION_RATE;
    }

    /**
     * Get week key for a date (format: YYYY-WW).
     *
     * @param \DateTime $date
     * @return string
     */
    private function getWeekKey(\DateTime $date): string
    {
        return $date->format('o-W'); // ISO year and week number
    }
}
