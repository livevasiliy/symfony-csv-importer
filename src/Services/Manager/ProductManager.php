<?php

declare(strict_types=1);

namespace App\Services\Manager;

use App\Entity\TblProductData;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager extends AbstractBaseManager
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(TblProductData::class);
    }
}
