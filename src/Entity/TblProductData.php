<?php

namespace App\Entity;

use App\Repository\TblProductDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tblProductData", uniqueConstraints={@ORM\UniqueConstraint(name="strProductCode", columns={"strProductCode"})})
 * @ORM\Entity(repositoryClass=TblProductDataRepository::class)
 */
class TblProductData
{
    /**
     * @var int
     *
     * @ORM\Column(name="intProductDataId", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $intproductdataid;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     */
    private $strproductname;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private $strproductdesc;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
     */
    private $strproductcode;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true, options={"default"="NULL"})
     */
    private $dtmadded = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true, options={"default"="NULL"})
     */
    private $dtmdiscontinued = 'NULL';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $stmtimestamp = 'current_timestamp()';

    /**
     * @return int
     */
    public function getIntproductdataid(): int
    {
        return $this->intproductdataid;
    }

    /**
     * @param int $intproductdataid
     */
    public function setIntproductdataid(int $intproductdataid): void
    {
        $this->intproductdataid = $intproductdataid;
    }

    /**
     * @return string
     */
    public function getStrproductname(): string
    {
        return $this->strproductname;
    }

    /**
     * @param string $strproductname
     */
    public function setStrproductname(string $strproductname): void
    {
        $this->strproductname = $strproductname;
    }

    /**
     * @return string
     */
    public function getStrproductdesc(): string
    {
        return $this->strproductdesc;
    }

    /**
     * @param string $strproductdesc
     */
    public function setStrproductdesc(string $strproductdesc): void
    {
        $this->strproductdesc = $strproductdesc;
    }

    /**
     * @return string
     */
    public function getStrproductcode(): string
    {
        return $this->strproductcode;
    }

    /**
     * @param string $strproductcode
     */
    public function setStrproductcode(string $strproductcode): void
    {
        $this->strproductcode = $strproductcode;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtmadded(): \DateTime|string|null
    {
        return $this->dtmadded;
    }

    /**
     * @param \DateTime|null $dtmadded
     */
    public function setDtmadded(\DateTime|string|null $dtmadded): void
    {
        $this->dtmadded = $dtmadded;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtmdiscontinued(): \DateTime|string|null
    {
        return $this->dtmdiscontinued;
    }

    /**
     * @param \DateTime|null $dtmdiscontinued
     */
    public function setDtmdiscontinued(\DateTime|string|null $dtmdiscontinued): void
    {
        $this->dtmdiscontinued = $dtmdiscontinued;
    }

    /**
     * @return \DateTime
     */
    public function getStmtimestamp(): \DateTime|string
    {
        return $this->stmtimestamp;
    }

    /**
     * @param \DateTime $stmtimestamp
     */
    public function setStmtimestamp(\DateTime|string $stmtimestamp): void
    {
        $this->stmtimestamp = $stmtimestamp;
    }
}
