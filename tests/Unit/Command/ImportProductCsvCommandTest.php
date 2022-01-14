<?php

namespace App\Tests\Unit\Command;

use App\Command\ImportProductCsvCommand;
use App\Entity\Product;
use App\Tests\ReflectionClass;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group unit
 */
class ImportProductCsvCommandTest extends KernelTestCase
{
    use ReflectionClass;

    private \PHPUnit\Framework\MockObject\MockObject|EntityManagerInterface $entityManagerMock;
    private ImportProductCsvCommand $command;
    private Application $application;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->command = new ImportProductCsvCommand($this->entityManagerMock);

        $kernel = self::bootKernel();
        $this->application = new Application($kernel);
        parent::setUp();
    }

    public function test_it_should_success_execute(): void
    {
        $command = $this->application->find('app:import-products:csv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['path' => 'tests/data/stock.csv']);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString(sprintf('successfully created product %d & %d has been updated', 2, 1), $output);
        $this->assertSame(0, $commandTester->getStatusCode());
    }

    public function test_it_should_success_serialization(): void
    {
        $getCsvRowAsArraysMock = self::invokeMethod($this->command, 'getCsvRowAsArrays', ['tests/data/stock.csv']);

        $expectArrays = [
            [
                "Product Code" => "P0001",
                "Product Name" => "TV",
                "Product Description" => "32” Tv",
                "Stock" => "10",
                "Cost in GBP" => "399.99",
                "Discontinued" => "",
            ],
            [
                "Product Code" => "P0002",
                "Product Name" => "Bluray Player",
                "Product Description" => "Excellent picture",
                "Stock" => "32",
                "Cost in GBP" => "$4.33",
                "Discontinued" => "",
            ],
            [
                "Product Code" => "P0002",
                "Product Name" => "Bluray Player",
                "Product Description" => "Excellent picture",
                "Stock" => "32",
                "Cost in GBP" => "4.33",
                "Discontinued" => "",
            ]
        ];
        self::assertEqualsCanonicalizing($expectArrays, $getCsvRowAsArraysMock);
        self::assertSame(3, count($getCsvRowAsArraysMock));
    }

    public function test_it_should_success_create_product(): void
    {
        $this->entityManagerMock->expects(self::once())->method('persist');
        $this->entityManagerMock->expects(self::once())->method('flush');

        $createProductMock = self::invokeMethod($this->command, 'createProduct', [
            [
                "Product Code" => "P0001",
                "Product Name" => "TV",
                "Product Description" => "32” Tv",
            ],
            null,
            399.99,
            10
        ]);

        self::assertSame(null, $createProductMock);
    }

    public function test_it_should_success_update_product(): void
    {
        $this->entityManagerMock->expects(self::once())->method('persist');
        $this->entityManagerMock->expects(self::once())->method('flush');

        $existProduct = self::createMock(Product::class);
        $existProduct->expects(self::once())->method('getStrProductCode')->willReturn('P0002');

        $updateProductMock = self::invokeMethod($this->command, 'updateProduct', [
            $existProduct,
            [
                "Product Name" => "TV",
                "Product Description" => "32” Tv",
            ],
            null,
            399.99,
            10
        ]);

        self::assertSame(null, $updateProductMock);
    }
}
