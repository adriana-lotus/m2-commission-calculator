<?php

declare(strict_types=1);

namespace LotusBloomProduction\ComissionCalculator\Console\Command;

use LotusBloomProduction\ComissionCalculator\Model\CommissionCalculatorFactory;
use LotusBloomProduction\ComissionCalculator\Service\CsvReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Calculate commission command.
 */
class CalculateCommission extends Command
{
    /**
     * Command name.
     */
    private const COMMAND_NAME = 'commission:calculate';

    /**
     * CSV file argument name.
     */
    private const CSV_FILE_ARGUMENT = 'csv-file';

    /**
     * @var CsvReader
     */
    private CsvReader $csvReader;

    /**
     * @var CommissionCalculatorFactory
     */
    private CommissionCalculatorFactory $calculatorFactory;

    /**
     * @param CsvReader $csvReader
     * @param CommissionCalculatorFactory $calculatorFactory
     * @param string|null $name
     */
    public function __construct(
        CsvReader $csvReader,
        CommissionCalculatorFactory $calculatorFactory,
        string $name = null
    ) {
        parent::__construct($name);
        $this->csvReader = $csvReader;
        $this->calculatorFactory = $calculatorFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Calculate commission fees for operations in a CSV file')
            ->addArgument(
                self::CSV_FILE_ARGUMENT,
                InputArgument::REQUIRED,
                'Path to the CSV file with operations'
            );

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument(self::CSV_FILE_ARGUMENT);

        try {
            $operations = $this->csvReader->read($filePath);

            foreach ($operations as $operation) {
                $calculator = $this->calculatorFactory->create($operation);
                $commission = $calculator->calculate($operation);

                // Output commission fee without currency
                $output->writeln(number_format($commission, 2, '.', ''));
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
