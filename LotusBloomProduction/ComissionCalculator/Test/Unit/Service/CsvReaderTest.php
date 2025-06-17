<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Test\Unit\Service;

use LotusBloomProduction\ComissionCalculator\Service\CsvReader;
use LotusBloomProduction\ComissionCalculator\Api\Data\OperationInterface;
use PHPUnit\Framework\TestCase;

/**
 * CSV Reader service test.
 */
class CsvReaderTest extends TestCase
{
    /**
     * @var CsvReader
     */
    private CsvReader $csvReader;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->csvReader = new CsvReader();
    }

    /**
     * Test reading a valid CSV file.
     */
    public function testReadValidCsvFile(): void
    {
        // Create test CSV file with valid data
        $csvContent = <<<CSV
2014-12-31,4,private,withdraw,1200.00,EUR
2015-01-01,4,private,withdraw,1000.00,EUR
2016-01-05,1,private,deposit,200.00,EUR
CSV;

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_reader_test');
        file_put_contents($tempFile, $csvContent);

        try {
            // Read operations from CSV file
            $operations = $this->csvReader->read($tempFile);

            // Assert that the correct number of operations was read
            $this->assertCount(3, $operations);

            // Assert that each operation is an instance of OperationInterface
            foreach ($operations as $operation) {
                $this->assertInstanceOf(OperationInterface::class, $operation);
            }

            // Verify the first operation's data
            $firstOperation = $operations[0];
            $this->assertEquals('2014-12-31', $firstOperation->getDate()->format('Y-m-d'));
            $this->assertEquals(4, $firstOperation->getUserId());
            $this->assertEquals('private', $firstOperation->getUserType());
            $this->assertEquals('withdraw', $firstOperation->getOperationType());
            $this->assertEquals(1200.00, $firstOperation->getAmount());
            $this->assertEquals('EUR', $firstOperation->getCurrency());
        } finally {
            // Clean up
            unlink($tempFile);
        }
    }

    /**
     * Test reading a CSV file with invalid rows.
     */
    public function testReadCsvFileWithInvalidRows(): void
    {
        // Create test CSV file with some invalid rows
        $csvContent = <<<CSV
2014-12-31,4,private,withdraw,1200.00,EUR
invalid,row,with,six,columns,test
2015-01-01,4,private,withdraw,1000.00,EUR
invalid,row,with,only,five
2016-01-05,1,private,deposit,200.00,EUR
CSV;

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_reader_test');
        file_put_contents($tempFile, $csvContent);

        try {
            // Read operations from CSV file
            $operations = $this->csvReader->read($tempFile);

            // Assert that only valid rows were processed (3 valid rows)
            $this->assertCount(3, $operations);

            // Verify the second valid operation's data (index 1)
            $secondOperation = $operations[1];
            $this->assertEquals('2015-01-01', $secondOperation->getDate()->format('Y-m-d'));
            $this->assertEquals(4, $secondOperation->getUserId());
        } finally {
            // Clean up
            unlink($tempFile);
        }
    }

    /**
     * Test reading a non-existent file.
     */
    public function testReadNonExistentFile(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File "non_existent_file.csv" does not exist');

        $this->csvReader->read('non_existent_file.csv');
    }

    /**
     * Test handling exceptions during operation creation.
     */
    public function testHandleExceptionsDuringOperationCreation(): void
    {
        // Create test CSV file with invalid date format that will cause exception
        $csvContent = <<<CSV
2014-12-31,4,private,withdraw,1200.00,EUR
invalid-date,4,private,withdraw,1000.00,EUR
2016-01-05,1,private,deposit,200.00,EUR
CSV;

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_reader_test');
        file_put_contents($tempFile, $csvContent);

        try {
            // Read operations from CSV file
            $operations = $this->csvReader->read($tempFile);

            // Assert that only valid rows were processed (2 valid rows)
            $this->assertCount(2, $operations);
        } finally {
            // Clean up
            unlink($tempFile);
        }
    }
}
