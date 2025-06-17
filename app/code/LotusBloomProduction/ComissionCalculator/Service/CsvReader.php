<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Service;

use LotusBloomProduction\ComissionCalculator\Api\Data\OperationInterface;
use LotusBloomProduction\ComissionCalculator\Model\Operation;

/**
 * CSV reader service.
 */
class CsvReader
{
    /**
     * Read operations from CSV file.
     *
     * @param string $filePath
     * @return OperationInterface[]
     * @throws \InvalidArgumentException
     */
    public function read(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(sprintf('File "%s" does not exist', $filePath));
        }

        $operations = [];
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new \InvalidArgumentException(sprintf('Could not open file "%s"', $filePath));
        }

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== 6) {
                continue; // Skip invalid rows
            }

            try {
                $operations[] = $this->createOperation($data);
            } catch (\Exception $e) {
                // Log error or handle invalid data
                continue;
            }
        }

        fclose($handle);
        return $operations;
    }

    /**
     * Create operation from CSV row data.
     *
     * @param array $data
     * @return OperationInterface
     * @throws \Exception
     */
    private function createOperation(array $data): OperationInterface
    {
        list($date, $userId, $userType, $operationType, $amount, $currency) = $data;

        return new Operation(
            new \DateTime($date),
            (int) $userId,
            strtolower($userType),
            strtolower($operationType),
            (float) $amount,
            strtoupper($currency)
        );
    }
}
