<?php

namespace Leaphly\Price;

class ConversionPriceTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $price = new Price(
            array(
                'EUR' => 5,
                'USD' => 10,
                'GBP' => 15,
            ),
            array('USD/CHF 1.500')
        );

        $this->assertInstanceOf(
            'Leaphly\Price\Price',
            $price
        );

        $this->assertEquals(15,
            $price->getAmount('CHF')
        );
    }

    public function testShouldAlsoHaveCHFInTheAvailableCurrencies()
    {
        $price = new Price(
            array(
                'EUR' => 5,
                'USD' => 10,
                'GBP' => 15,
            ),
            array('USD/CHF 1.500')
        );

        $this->assertEquals('EUR-USD-GBP-CHF',
            join('-',$price->availableCurrencies())
        );
    }

    public function testCurrencyPairsConversions()
    {
        $price = new Price(
            array(
                'EUR' => 5,
                'USD' => 10,
                'GBP' => 15,
            ),
            array(
                'USD/CHF 1.500',
                'EUR/GBP 1.200'
            )
        );

        $conversions = $price->getConversions();

        $this->assertCount(2, $conversions);
        $this->assertInstanceOf('Money\CurrencyPair', $conversions[0]);
        $this->assertEquals('USD', $conversions[0]->getBaseCurrency());
        $this->assertEquals('CHF', $conversions[0]->getCounterCurrency());
        $this->assertEquals(1.5,   $conversions[0]->getRatio());
    }

    public function testConversionRecursive()
    {
        $price = new Price(
            array(
                'EUR' => 1
            ),
            array(
                'EUR/USD 2',
                'USD/CHF 2'
            )

        );

        $this->assertInstanceOf(
            'Leaphly\Price\Price',
            $price
        );

        $this->assertEquals(4,
            $price->getAmount('CHF')
        );
    }
}
