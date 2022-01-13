<?php

declare(strict_types=1);

namespace App\Command;

use App\DTO\TblProductDataDTO;
use App\Entity\TblProductData;
use App\Services\Currency\FreeCurrencyApi;
use App\Services\Manager\ProductManager;
use DateTimeImmutable;
use League\Csv\Reader;
use League\Csv\Exception as CsvException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use function in_array;
use function count;
use function clearSymbolsFromDecimalString;
use function containSymbolsInDecimalString;

class ImportProductCsvCommand extends Command
{
    // TODO: Cover mock tests

    private const MINIMUM_COST = 5;
    private const MINIMUM_STOCK = 10;
    private const HIGH_COST = 1000;

    protected static $defaultName = 'app:import-products:csv';
    protected static $defaultDescription = 'Import products from CSV';

    private FreeCurrencyApi $currencyConverter;
    private ProductManager $productManager;

    public function __construct(ProductManager $productManager, FreeCurrencyApi $currencyConverter)
    {
        parent::__construct();

        $this->currencyConverter = $currencyConverter;
        $this->productManager = $productManager;
    }

    protected function configure(): void
    {
        // TODO: Add option & write logic for test mode (`--test` option)
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // TODO: Added in $failedProducts also which fall validation by validator
        $failedProducts = [];
        $createdProducts = [];
        $updatedProducts = [];
        $skippedProducts = [];

        $io = new SymfonyStyle($input, $output);

        /**
         * @phpstan-var string
         * @var string $path
         */
        $path = $input->getArgument('path');

        $filesystem = new Filesystem();

        if (!$filesystem->exists($path)) {
            throw new RuntimeException(sprintf('File "%s" does not exist', $path), Command::FAILURE);
        } elseif (!in_array(pathinfo($path, PATHINFO_EXTENSION), $this->getSupportedExtensions(), true)) {
            throw new RuntimeException(sprintf('File "%s not supported', $path), Command::FAILURE);
        }

        // TODO: Handle unexpected exception from external API & service
        $currencyRates = $this->currencyConverter->getLastRates('GBP');
        $currentCurrencyRate = number_format($this->currencyConverter->extractCurrentRateForCurrency($currencyRates, 'USD'), 2);

        try {
            $csv = Reader::createFromPath($path);
            $csv->setHeaderOffset(0);
        } catch (CsvException $e) {
            throw new RuntimeException($e->getMessage(), Command::FAILURE, $e);
        }

        $records = $csv->getRecords();

        $processBar = $io->createProgressBar(count(iterator_to_array($records)));
        $processBar->start();

        foreach ($records as $record) {
            if (!isset($record['Cost in GBP'])) {
                $failedProducts[$record['Product Code']][] = 'Invalid cost in GBP';
                continue;
            } else if (!isset($record['Stock'])) {
                $failedProducts[$record['Product Code']][] = 'Invalid stock';
                continue;
            }

            $cost = trim($record['Cost in GBP']);
            $stock = trim($record['Stock']) !== '' ? (int)$record['Stock'] : null;

            if (containSymbolsInDecimalString($cost)) {
                $cost = (float)clearSymbolsFromDecimalString($cost);
            } else {
                $cost = (float)$cost * (float)$currentCurrencyRate;
            }

            $cost = (float)number_format($cost, 2);

            // If cost less that $5 and has less than 10 stock - not imported.
            if ($cost < self::MINIMUM_COST && $stock < self::MINIMUM_STOCK) {
                $skippedProducts[] = $record['Product Code'];
                continue;
                // Any stock which cost over $1000 - not imported
            } elseif ($cost > self::HIGH_COST) {
                $skippedProducts[] = $record['Product Code'];
                continue;
            }
            // Any stock marked as discounted - imported with current datetime
            $discontinued = null;

            if (isset($record['Discontinued']) && $record['Discontinued'] === 'yes') {
                $discontinued = new DateTimeImmutable();
            }

            $existProduct = $this->productManager
                ->getRepository()
                ->findOneBy(['strProductCode' => $record['Product Code']]);

            $product = new TblProductData();

            if (null !== $existProduct && $existProduct->getIntProductDataId()) {
                $existProduct->setStrProductName($record['Product Name']);
                $existProduct->setStrProductDesc($record['Product Description']);
                $existProduct->setStrProductCode($existProduct->getStrProductCode());
                $existProduct->setStock($stock);
                $existProduct->setCost($cost);
                $existProduct->setDtmDiscontinued($discontinued);
                $this->productManager->save($existProduct);
                $updatedProducts[] = $record['Product Code'];
            } else {
                $product->setStrProductName($record['Product Name']);
                $product->setStrProductDesc($record['Product Description']);
                $product->setStrProductCode($record['Product Code']);
                $product->setDtmDiscontinued($discontinued);
                $product->setDtmAdded(new DateTimeImmutable());
                $product->setCost($cost);
                $product->setStock($stock);

                $this->productManager->save($product);
                $createdProducts[] = $record['Product Code'];
            }
        }

        $processBar->finish();

        $io->success(sprintf('%d product(s) has been imported & %d has been updated.', count($createdProducts), count($updatedProducts)));
        $io->error(sprintf('%d product(s) has been failed to import', count($failedProducts)));
        $io->warning(sprintf('%d product(s) has been skipped to import', count($skippedProducts)));

        return Command::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function getSupportedExtensions(): array
    {
        return ['csv'];
    }
}
