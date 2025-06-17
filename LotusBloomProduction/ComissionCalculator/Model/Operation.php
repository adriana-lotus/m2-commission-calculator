<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Model;

use LotusBloomProduction\ComissionCalculator\Api\Data\OperationInterface;

/**
 * Operation model.
 */
class Operation implements OperationInterface
{
    /**
     * @var \DateTime
     */
    private \DateTime $date;

    /**
     * @var int
     */
    private int $userId;

    /**
     * @var string
     */
    private string $userType;

    /**
     * @var string
     */
    private string $operationType;

    /**
     * @var float
     */
    private float $amount;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @param \DateTime $date
     * @param int $userId
     * @param string $userType
     * @param string $operationType
     * @param float $amount
     * @param string $currency
     */
    public function __construct(
        \DateTime $date,
        int $userId,
        string $userType,
        string $operationType,
        float $amount,
        string $currency
    ) {
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @inheritdoc
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @inheritdoc
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @inheritdoc
     */
    public function getOperationType(): string
    {
        return $this->operationType;
    }

    /**
     * @inheritdoc
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}
