<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass:ProductRepository::class)]
#[ORM\Table(name: 'product')]
#[ORM\UniqueConstraint(name: 'str_product_code', columns: ['str_product_code'])]
#[UniqueEntity(fields: ['str_product_code'])]
class Product
{
    public const STRING_MAX_LENGTH = 255;
    public const PRODUCT_NAME_MAX_LENGTH = 50;
    public const PRODUCT_CODE_MAX_LENGTH = 10;
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    public const DECIMAL_REGEXP = '/^\d{0,10}(\.\d{0,2})?/';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: self::PRODUCT_NAME_MAX_LENGTH, nullable: true)]
    #[Assert\Length(max: self::PRODUCT_NAME_MAX_LENGTH, normalizer: 'trim')]
    private ?string $strProductName;

    #[ORM\Column(type: 'string', length: self::STRING_MAX_LENGTH, nullable: true)]
    #[Assert\Length(max: self::STRING_MAX_LENGTH, normalizer: 'trim')]
    private ?string $strProductDesc;

    #[ORM\Column(type: 'string', length: self::PRODUCT_CODE_MAX_LENGTH, unique: true, nullable: false)]
    #[Assert\Length(max: self::PRODUCT_CODE_MAX_LENGTH, normalizer: 'trim')]
    private ?string $strProductCode;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\DateTime(format: self::DATE_TIME_FORMAT)]
    private ?DateTimeInterface $dtmAdded;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\DateTime(format: self::DATE_TIME_FORMAT)]
    private ?DateTimeInterface $dtmDiscontinued;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTimeInterface $stmTimestamp;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: self::DECIMAL_REGEXP)]
    private float $cost;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Blank]
    private ?int $stock;

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStrProductName(): ?string
    {
        return $this->strProductName;
    }

    public function setStrProductName(string $strProductName): self
    {
        $this->strProductName = $strProductName;

        return $this;
    }

    public function getStrProductDesc(): ?string
    {
        return $this->strProductDesc;
    }

    public function setStrProductDesc(string $strProductDesc): self
    {
        $this->strProductDesc = $strProductDesc;

        return $this;
    }

    public function getStrProductCode(): ?string
    {
        return $this->strProductCode;
    }

    public function setStrProductCode(string $strProductCode): self
    {
        $this->strProductCode = $strProductCode;

        return $this;
    }

    public function getDtmAdded(): ?DateTimeInterface
    {
        return $this->dtmAdded;
    }

    public function setDtmAdded(?DateTimeInterface $dtmAdded): self
    {
        $this->dtmAdded = $dtmAdded;

        return $this;
    }

    public function getDtmDiscontinued(): ?DateTimeInterface
    {
        return $this->dtmDiscontinued;
    }

    public function setDtmDiscontinued(?DateTimeInterface $dtmDiscontinued): self
    {
        $this->dtmDiscontinued = $dtmDiscontinued;

        return $this;
    }

    public function getStmTimestamp(): ?DateTimeInterface
    {
        return $this->stmTimestamp;
    }

    public function setStmTimestamp(DateTimeInterface $stmTimestamp): self
    {
        $this->stmTimestamp = $stmTimestamp;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}
