<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TblProductDataRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(
 * name="tblProductData", uniqueConstraints={@ORM\UniqueConstraint(name="strProductCode", columns={"strProductCode"})})
 * )
 * @UniqueEntity(fields={"strProductCode"})
 * @ORM\Entity(repositoryClass=TblProductDataRepository::class)
 */
class TblProductData
{
    /**
     * @var int
     *
     * @ORM\Column(name="intProductDataId", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $intProductDataId;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     */
    private string $strProductName;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private string $strProductDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, unique=true, nullable=false)
     */
    private string $strProductCode;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true, options={"default": "NULL"})
     */
    private ?DateTimeInterface $dtmAdded;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true, options={"default": "NULL"})
     */
    private ?DateTimeInterface $dtmDiscontinued;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false, options={"default": "current_timestamp()"})
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Timestampable(on="update")
     */
    private DateTimeInterface $stmTimestamp;

    /**
     * @ORM\Column(name="cost", type="decimal", scale=2, precision=10)
     *
     * @var float
     */
    private float $cost;

    /**
     * @ORM\Column(name="stock", type="integer", nullable=true)
     *
     * @var int|null
     */
    private ?int $stock;

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @param float $cost
     */
    public function setCost(float $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return int|null
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * @param int|null $stock
     */
    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }

    /**
     * @return int
     */
    public function getIntProductDataId(): int
    {
        return $this->intProductDataId;
    }

    /**
     * @param int $intProductDataId
     */
    public function setIntProductDataId(int $intProductDataId): void
    {
        $this->intProductDataId = $intProductDataId;
    }

    /**
     * @return string
     */
    public function getStrProductName(): string
    {
        return $this->strProductName;
    }

    /**
     * @param string $strProductName
     */
    public function setStrProductName(string $strProductName): void
    {
        $this->strProductName = $strProductName;
    }

    /**
     * @return string
     */
    public function getStrProductDesc(): string
    {
        return $this->strProductDesc;
    }

    /**
     * @param string $strProductDesc
     */
    public function setStrProductDesc(string $strProductDesc): void
    {
        $this->strProductDesc = $strProductDesc;
    }

    /**
     * @return string
     */
    public function getStrProductCode(): string
    {
        return $this->strProductCode;
    }

    /**
     * @param string $strProductCode
     */
    public function setStrProductCode(string $strProductCode): void
    {
        $this->strProductCode = $strProductCode;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDtmAdded(): ?DateTimeInterface
    {
        return $this->dtmAdded;
    }

    /**
     * @param DateTimeInterface $dtmAdded
     */
    public function setDtmAdded(DateTimeInterface $dtmAdded): void
    {
        $this->dtmAdded = $dtmAdded;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDtmDiscontinued(): ?DateTimeInterface
    {
        return $this->dtmDiscontinued;
    }

    /**
     * @param DateTimeInterface|null $dtmDiscontinued
     */
    public function setDtmDiscontinued(?DateTimeInterface $dtmDiscontinued): void
    {
        $this->dtmDiscontinued = $dtmDiscontinued;
    }

    /**
     * @return DateTimeInterface
     */
    public function getStmTimestamp(): DateTimeInterface
    {
        return $this->stmTimestamp;
    }
}
