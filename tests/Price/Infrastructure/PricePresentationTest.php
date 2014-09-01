<?php

namespace Leaphly\Price;

use Leaphly\Price\Infrastructure\PricePresentation;
use Leaphly\Price\Price;

class PricePresentationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider toString
     */
    public function testStringifyPrice(Price $price, $stringToAssert)
    {
        $string = PricePresentation::stringifyPrice($price);

        $this->assertEquals($stringToAssert, $string);
    }

    /**
     * @dataProvider toString
     */
    public function testPriceFromString(Price $priceAssert, $string)
    {
        $price = PricePresentation::createPriceFromString($string);

        $this->assertEquals($priceAssert, $price);
    }

    public function toString()
    {
        return array(
            array(new Price(['EUR'=>1000, 'GBP'=>2000], ['GBP/USD 1.3', 'GBP/CHF 1.5']),
                'EUR 1000,GBP 2000;GBP/USD 1.3,GBP/CHF 1.5'
            ),
            array(new Price(['EUR'=>1000], ['EUR/USD 1.3']),
                'EUR 1000;EUR/USD 1.3'
            ),
            array(new Price(['EUR'=>1000]),
                'EUR 1000'
            ),
        );
    }
}