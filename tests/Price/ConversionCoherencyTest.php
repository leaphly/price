<?php

namespace Leaphly\Price;

class ConversionCoherencyTest extends \PHPUnit_Framework_TestCase
{
    public function testAddingTwoPricesWithOneConversionShouldRemoveConvertedConversion()
    {
        $price1 = new Price(
            ['EUR'=>100, 'GBP'=>101]
        );
        $price2 = new Price(
            ['EUR'=>100],
            ['EUR/GBP 1.100'] // the GBP for this Price is calculated, should not in the new Price
        );

        $price = $price1->add($price2);

        $this->assertEquals(new Price(['EUR'=>200, 'GBP'=>211]), $price);
        $this->assertEmpty($price->getConversions());
    }

    public function testAddingTwoPricesWithOneConversionOnFirstAddendumShouldRemoveConvertedConversion()
    {
        $price1 = new Price(
            ['EUR'=>100],
            ['EUR/GBP 1.100']
        );

        $price2 = new Price(
            ['EUR'=>100, 'GBP'=>101]
        );

        $price = $price1->add($price2);

        $this->assertEquals(new Price(['EUR'=>200, 'GBP'=>211]), $price);
        $this->assertEmpty($price->getConversions());
    }

    public function testTwoPricesWithSameMoneyAndDifferentConversionShouldNotBeEqual()
    {
        $price1 = new Price(
            ['EUR'=>100],
            ['EUR/GBP 1.100']
        );

        $price2 = new Price(
            ['EUR'=>100]
        );

        $this->assertFalse($price1->equals($price2));
        $this->assertFalse($price2->equals($price1));
    }

    public function testMultiplyingAPricesWithOneConversionShouldNotRemoveConversion()
    {
        $price1 = new Price(
            ['EUR'=>100, 'GBP'=>101],
            ['EUR/USD 1.100']
        );

        $price = $price1->multiply(2);

        $this->assertEquals(new Price(['EUR'=>200, 'GBP'=>202], ['EUR/USD 1.100']), $price);
    }

    public function testShouldBeImmutable()
    {
        $p1ArrayMoney = ['EUR'=>100, 'GBP'=>101];
        $p2ArrayMoney = ['EUR'=>100];
        $p2ArrayCurrency = ['EUR/GBP 1.100'];

        $price1 = new Price($p1ArrayMoney);
        $price2 = new Price($p2ArrayMoney, $p2ArrayCurrency);

        $priceAdd = $price1->add($price2);
        $priceMul = $price1->multiply(2);

        $this->assertEquals(new Price($p1ArrayMoney), $price1);
        $this->assertEquals(new Price($p2ArrayMoney, $p2ArrayCurrency), $price2);
    }
}