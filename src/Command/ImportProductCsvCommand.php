<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


#[AsCommand(
    name: 'app:import-products:csv',
    description: 'Import products from CSV',
)]
class ImportProductCsvCommand extends Command
{
    private const REQUIRED_EXTENSION = 'csv';

    private const MINIMUM_COST = 5;
    private const MINIMUM_STOCK = 10;
    private const HIGH_COST = 1000;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'Path to CSV file')
            ->addOption('test', 't', InputOption::VALUE_OPTIONAL, 'Run in test mode')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Initialize
        $productManager = $this->entityManager->getRepository(Product::class);

        $updateProducts = 0;
        $createProduct = 0;
        $failedProducts = [];
        $skippedProducts = 0;

        $io = new SymfonyStyle($input, $output);

        /** @var string $file */
        $file = $input->getArgument('path');

        // First exists required file
        $filesystem = new Filesystem();

        if (!$filesystem->exists($file)) {
            $io->error(sprintf('File "%s" does not exist', $file));

            return Command::FAILURE;
        }

        // Check extension required file SHOULD be `.csv`
        if (!in_array(pathinfo($file, PATHINFO_EXTENSION), [self::REQUIRED_EXTENSION], true)) {
            $io->error(sprintf('Not a valid extension for file %s required .%s.', $file, self::REQUIRED_EXTENSION));

            return Command::INVALID;
        }

        // Serialize got file to array
        $products = $this->getCsvRowAsArrays($file);

        // Loop over records
        foreach ($products as $row) {

            if (!isset($row['Cost in GBP'])) {
                $failedProducts[$row['Product Code']][] = 'Invalid cost in GBP';
                continue;
            } else if (!isset($row['Stock'])) {
                $failedProducts[$row['Product Code']][] = 'Invalid stock';
                continue;
            }

            $cost = trim($row['Cost in GBP']);
            $stock = trim($row['Stock']) !== '' ? (int)$row['Stock'] : null;

            if (containSymbolsInDecimalString($cost)) {
                $cost = (float)clearSymbolsFromDecimalString($cost);
            }

            $cost = (float)number_format((float) $cost, 2);


            // Check some import rules
            if ($cost < self::MINIMUM_COST && $stock < self::MINIMUM_STOCK) {
                $skippedProducts++;
                continue;
            } elseif ($cost > self::HIGH_COST) {
                $skippedProducts++;
                continue;
            }

            $discontinued = isset($row['Discontinued']) && $row['Discontinued'] === 'yes'
                ? new DateTimeImmutable()
                : null;

            // Update IF matching records found in DB

            /** @var Product $existProduct */
            if ($existProduct = $productManager->findOneBy(['strProductCode' => $row['Product Code']])) {
                $this->updateProduct($existProduct, $row, $discontinued, $cost, $stock);
                $updateProducts++;

                continue;
            }
            // Create new records IF matching records not found in DB
            $this->createProduct($row, $discontinued, $cost, $stock);

            $createProduct++;
        }

        // Todo: IF enable test mode, we not execute prepare early SQL by Doctrine.


        // Return report
        $io->success(sprintf('successfully created product %d & %d has been updated', $createProduct, $updateProducts));

        // Exit
        return Command::SUCCESS;
    }

    private function getCsvRowAsArrays(string $path): array
    {
        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        return $decoder->decode(file_get_contents($path), 'csv');
    }

    /**
     * @param Product $existProduct
     * @param array $row
     * @param DateTimeImmutable|null $discontinued
     * @param float $cost
     * @param int|null $stock
     */
    private function updateProduct(Product $existProduct, array $row, ?DateTimeImmutable $discontinued, float $cost, ?int $stock): void
    {
        $existProduct->setStrProductName($row['Product Name']);
        $existProduct->setStrProductDesc($row['Product Description']);
        $existProduct->setStrProductCode($existProduct->getStrProductCode());
        $existProduct->setStock($stock);
        $existProduct->setCost($cost);
        $existProduct->setDtmDiscontinued($discontinued);

        $this->entityManager->persist($existProduct);
        $this->entityManager->flush();
    }

    /**
     * @param array $row
     * @param DateTimeImmutable|null $discontinued
     * @param float $cost
     * @param int|null $stock
     */
    private function createProduct(array $row, ?DateTimeImmutable $discontinued, float $cost, ?int $stock): void
    {
        $product = new Product();
        $product->setStrProductName($row['Product Name']);
        $product->setStrProductDesc($row['Product Description']);
        $product->setStrProductCode($row['Product Code']);
        $product->setStock($stock);
        $product->setCost($cost);
        $product->setDtmDiscontinued($discontinued);
        $product->setDtmAdded(new DateTimeImmutable());

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}
