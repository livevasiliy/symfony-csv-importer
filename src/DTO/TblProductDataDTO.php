<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\TblProductData;
use DateTimeInterface;

class TblProductDataDTO
{
    public const STRING_MAX_LENGTH = 255;
    public const PRODUCT_NAME_MAX_LENGTH = 50;
    public const PRODUCT_CODE_MAX_LENGTH = 10;
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    public const DECIMAL_REGEXP = '/^\d{0,10}(\.\d{0,2})?/';

    public ?int $intProductDataId;

    public string $strProductName;

    public string $strProductDesc;

    public string $strProductCode;

    public ?DateTimeInterface $dtmAdded;

    public ?DateTimeInterface $dtmDiscontinued;

    public float $cost;

    public ?int $stock;

    public static function makeFromArray(array $data): TblProductData
    {
        $model = new TblProductData();

        $model->setStrProductName($data['strProductName']);
        $model->setStrProductDesc($data['strProductDesc']);
        $model->setStrProductCode($data['strProductCode']);
        $model->setCost($data['cost']);
        $model->setStock($data['stock']);
        $model->setDtmDiscontinued($data['dtmDiscontinued']);
        $model->setDtmAdded($data['dtmAdded']);

        return $model;
    }
}
