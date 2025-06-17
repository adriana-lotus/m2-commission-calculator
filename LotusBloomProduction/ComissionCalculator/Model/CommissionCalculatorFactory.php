<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Model;

use LotusBloomProduction\ComissionCalculator\Api\ComissionCalculatorInterface;
use LotusBloomProduction\ComissionCalculator\Api\Data\OperationInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Commission calculator factory.
 */
class CommissionCalculatorFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @var array
     */
    private array $calculators;

    /**
     * @var array
     */
    private array $instances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $calculators
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $calculators = []
    ) {
        $this->objectManager = $objectManager;
        $this->calculators = $calculators;
    }

    /**
     * Create calculator instance based on operation type and user type.
     *
     * @param OperationInterface $operation
     * @return ComissionCalculatorInterface
     * @throws \InvalidArgumentException
     */
    public function create(OperationInterface $operation): ComissionCalculatorInterface
    {
        $operationType = $operation->getOperationType();
        $userType = $operation->getUserType();
        $key = $operationType . '_' . $userType;

        if (!isset($this->calculators[$key])) {
            throw new \InvalidArgumentException(
                sprintf('No calculator found for operation type "%s" and user type "%s"', $operationType, $userType)
            );
        }

        $calculatorClass = $this->calculators[$key];

        if (!isset($this->instances[$calculatorClass])) {
            $this->instances[$calculatorClass] = $this->objectManager->create($calculatorClass);
        }

        return $this->instances[$calculatorClass];
    }
}
