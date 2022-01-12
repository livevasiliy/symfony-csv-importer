<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\TblProductData;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TblProductDataDTO
{
    public const STRING_MAX_LENGTH = 255;
    public const PRODUCT_NAME_MAX_LENGTH = 50;
    public const PRODUCT_CODE_MAX_LENGTH = 10;
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    public const DECIMAL_REGEXP = '/^\d{0,10}(\.\d{0,2})?/';

    /**
     * @Assert\NotBlank
     * @Assert\Integer
     */
    public ?int $intProductDataId;

    /**
     * @Assert\NotBlank
     * @Assert\String
     * @Assert\Length(max=self::PRODUCT_NAME_MAX_LENGTH, normalizer="trim")
     */
    public string $strProductName;

    /**
     * @Assert\NotBlank
     * @Assert\String
     * @Assert\Length(max=self::STRING_MAX_LENGTH, normalizer="trim")
     */
    public string $strProductDesc;

    /**
     * @Assert\NotBlank
     * @Assert\String
     * @Assert\Length(max=self::PRODUCT_CODE_MAX_LENGTH, normalizer="trim")
     */
    public string $strProductCode;

    /**
     * @Assert\Blank
     * @Assert\DateTime(format=self::DATE_TIME_FORMAT)
     */
    public ?DateTimeInterface $dtmAdded;

    /**
     * @Assert\Blank
     * @Assert\DateTime(format=self::DATE_TIME_FORMAT)
     */
    public ?DateTimeInterface $dtmDiscontinued;

    /**
     * @Assert\NotBlank
     * @Assert\Regex(self::DECIMAL_REGEXP)
     */
    public float $cost;

    /**
     * @Assert\Blank
     * @Assert\Integer
     */
    public ?int $stock;

    public static function makeFromArray(array $data): self
    {
        $model = new self();

        if ($data === []) {
            return $model;
        }

        $model->intProductDataId = $data['intProductDataId'];
        $model->strProductName = $data['strProductName'];
        $model->strProductDesc = $data['strProductDesc'];
        $model->strProductCode = $data['strProductCode'];
        $model->cost = $data['cost'];
        $model->stock = $data['stock'];
        $model->dtmDiscontinued = $data['dtmDiscontinued'];
        $model->dtmAdded = $data['dtmAdded'];

        return $model;
    }
}
