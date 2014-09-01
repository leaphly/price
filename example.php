<?php

$loader = require __DIR__ . "/vendor/autoload.php";

use Leaphly\Price\Price;
use Leaphly\Price\Infrastructure\PricePresentation;

$price1 = new Price(
    ['EUR'=>100, 'GBP'=>100]
);

$price1 = $price1->multiply(10);

$price2 = new Price(
    ['EUR'=>100],
    ['EUR/GBP 1.100']
);

$priceSum = $price1->add($price2);

echo 'price1 = ' . PricePresentation::stringifyPrice($price1).'.'.PHP_EOL;
echo 'price2 = ' . PricePresentation::stringifyPrice($price2).'.'.PHP_EOL;
echo 'price1+price2 = ' . PricePresentation::stringifyPrice($priceSum).'.'.PHP_EOL;

echo 'the amount in EUR:'.$priceSum->inEUR().'.'.PHP_EOL;
echo 'the amount in GBP:'.$priceSum->inGBP().'.'.PHP_EOL;

echo 'is Zero?'.($price1->isZero()?' true':' false').'.'.PHP_EOL;
echo 'is price1 equals to price2?'.($price1->equals($price2)?' true':' false').'.'.PHP_EOL;