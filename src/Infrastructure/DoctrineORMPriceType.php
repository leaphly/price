<?php

namespace Leaphly\Price\Infrastructure;

use Leaphly\Price\Price;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DoctrineORMPriceType extends Type
{
    const NAME = 'price';

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return null;
        }

        return PricePresentation::createPriceFromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Price) {
            return (string) PricePresentation::stringifyPrice($value);
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    public function getName()
    {
        return self::NAME;
    }
}
