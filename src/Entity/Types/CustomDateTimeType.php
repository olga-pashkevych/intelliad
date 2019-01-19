<?php

namespace App\Entity\Types;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class CustomDateTimeType extends DateTimeType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $dateTime = parent::convertToPHPValue($value, $platform);

        if ( ! $dateTime) {
            return $dateTime;
        }

        return new CustomDateTime('@' . $dateTime->format('U'));
    }

    public function getName()
    {
        return 'custom_datetime';
    }
}