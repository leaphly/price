<?php

namespace Leaphly\Price;

use Money\CurrencyPair;
use Money\Money;

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

    public function testConstructorWithMoney()
    {
        $price = new Price(
            [
                Money::EUR(5),
                Money::USD(10),
                Money::GBP(10),
                'TRY' => 120
            ],
            [
                CurrencyPair::createFromIso('USD/CHF 1.5'),
                CurrencyPair::createFromIso('USD/AWG 1.0')
            ]
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
                'USD/CHF 1.500'
            )
        );

        $conversions = $price->getConversions();

        $this->assertCount(1, $conversions);
        $this->assertInstanceOf('Money\CurrencyPair', $conversions[0]);
        $this->assertEquals('USD', $conversions[0]->getBaseCurrency());
        $this->assertEquals('CHF', $conversions[0]->getCounterCurrency());
        $this->assertEquals(1.5,   $conversions[0]->getRatio());
    }


    /**
     * @expectedException Exception
     */
    public function testShouldThrowExceptionIfAConversionIsAlreadyInTheCurrencies()
    {
        new Price(
            array(
                'EUR' => 5,
                'USD' => 10,
                'GBP' => 15,
            ),
            array(
                'USD/CHF 1.500',
                'USD/GBP 1.500'
            )
        );
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