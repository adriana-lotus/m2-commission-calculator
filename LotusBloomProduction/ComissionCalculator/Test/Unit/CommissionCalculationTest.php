<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Test\Unit;

use LotusBloomProduction\ComissionCalculator\Model\Operation;
use LotusBloomProduction\ComissionCalculator\Service\CsvReader;
use LotusBloomProduction\ComissionCalculator\Service\CurrencyConverter;
use LotusBloomProduction\ComissionCalculator\Service\CommissionRounder;
use LotusBloomProduction\ComissionCalculator\Model\Calculator\DepositCalculator;
use LotusBloomProduction\ComissionCalculator\Model\Calculator\PrivateWithdrawCalculator;
use LotusBloomProduction\ComissionCalculator\Model\Calculator\BusinessWithdrawCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Commission calculation test.
 */
class CommissionCalculationTest extends TestCase
{
    /**
     * Test commission calculation with example input.
     */
    public function testCommissionCalculation(): void
    {
        // Create test CSV file with example input
        $csvContent = <<<CSV
2014-12-31,4,private,withdraw,1200.00,EUR
2015-01-01,4,private,withdraw,1000.00,EUR
2016-01-05,4,private,withdraw,1000.00,EUR
2016-01-05,1,private,deposit,200.00,EUR
2016-01-06,2,business,withdraw,300.00,EUR
2016-01-06,1,private,withdraw,30000,JPY
2016-01-07,1,private,withdraw,1000.00,EUR
2016-01-07,1,private,withdraw,100.00,USD
2016-01-10,1,private,withdraw,100.00,EUR
2016-01-10,2,business,deposit,10000.00,EUR
2016-01-10,3,private,withdraw,1000.00,EUR
2016-02-15,1,private,withdraw,300.00,EUR
2016-02-19,5,private,withdraw,3000000,JPY
CSV;

        $tempFile = tempnam(sys_get_temp_dir(), 'commission_test');
        file_put_contents($tempFile, $csvContent);

        // Expected output based on example
        $expectedOutput = [
            '0.60',
            '3.00',
            '0.00',
            '0.06',
            '1.50',
            '0',
            '0.70',
            '0.30',
            '0.30',
            '3.00',
            '0.00',
            '0.00',
            '8612'
        ];

        try {
            // Create services
            $csvReader = new CsvReader();
            $currencyConverter = new CurrencyConverter();
            $commissionRounder = new CommissionRounder();

            // Create calculators
            $depositCalculator = new DepositCalculator($commissionRounder);
            $privateWithdrawCalculator = new PrivateWithdrawCalculator($commissionRounder, $currencyConverter);
            $businessWithdrawCalculator = new BusinessWithdrawCalculator($commissionRounder);

            // Read operations from CSV file
            $operations = $csvReader->read($tempFile);

            // Process operations and calculate commissions
            $actualOutput = [];
            foreach ($operations as $index => $operation) {
                // Select calculator based on operation type and user type
                $calculator = null;
                $operationType = $operation->getOperationType();
                $userType = $operation->getUserType();

                if ($operationType === 'deposit') {
                    $calculator = $depositCalculator;
                } elseif ($operationType === 'withdraw') {
                    if ($userType === 'private') {
                        $calculator = $privateWithdrawCalculator;
                    } elseif ($userType === 'business') {
                        $calculator = $businessWithdrawCalculator;
                    }
                }

                // Calculate commission
                $commission = $calculator->calculate($operation);

                // Format output based on currency
                $currency = $operation->getCurrency();
                if ($currency === 'JPY') {
                    // JPY has 0 decimal places
                    $actualOutput[] = number_format($commission, 0, '.', '');
                } else {
                    // Default to 2 decimal places
                    $actualOutput[] = number_format($commission, 2, '.', '');
                }
            }

            // Assert that actual output matches expected output
            $this->assertEquals($expectedOutput, $actualOutput);
        } finally {
            // Clean up
            unlink($tempFile);
        }
    }
}
