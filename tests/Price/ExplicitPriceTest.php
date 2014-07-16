<?php

namespace Leaphly\Price;

use Leaphly\Price\Price;

class ExplicitPriceTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $price = new Price(
            [
                'EUR' => 10,
                'USD' => 120,
                'GBP' => 12,
            ]
        );

        $this->assertInstanceOf(
            'Leaphly\Price\Price',
            $price
        );

        $this->assertEquals(10,
            $price->getAmount('EUR')
        );
    }


    public function testMagicCall()
    {
        $price = new Price();
        $price->setEUR(20);

        $this->assertInstanceOf(
            'Leaphly\Price\Price',
            $price
        );
    }

    public function testAddEur()
    {
        $price = new Price();
        $price->set('EUR', 20);

        $this->assertInstanceOf(
            'Leaphly\Price\Price',
            $price
        );
    }

    public function testGetAmount()
    {
        $price = new Price();
        $price->set('EUR', 20);

        $this->assertEquals(20,
            $price->getAmount('EUR')
        );
    }

    public function testAddPrices()
    {
        $price1 = new Price();
        $price1->set('EUR', 20);

        $price2 = new Price();
        $price2->set('EUR', 10);

        $this->assertEquals(30,
            $price1->add($price2)->getAmount('EUR')
        );
    }


    public function testEquals()
    {
        $price1 = new Price();
        $price1->set('EUR', 10);

        $price2 = new Price();
        $price2->set('EUR', 10);

        $this->assertTrue(
            $price1->equals($price2)
        );
    }

    public function testEqualsOnDifferent()
    {
        $price1 = new Price();
        $price1->set('GBP', 1);
        $price1->set('USD', 10);

        $price2 = new Price();
        $price2->set('USD', 1);
        $price2->set('EUR', 10);

        $this->assertFalse(
            $price1->equals($price2)
        );
    }

    public function testSubtractPricesNegative()
    {
        $price1 = new Price();
        $price1->set('EUR', 20);

        $price2 = new Price();
        $price2->set('EUR', 10);

        $this->assertEquals(10,
            $price1->subtract($price2)->getAmount('EUR')
        );
    }

    public function testSubtractPrices()
    {
        $price1 = new Price();
        $price1->set('EUR', 10);

        $price2 = new Price();
        $price2->set('EUR', 20);

        $this->assertEquals(-10,
            $price1->subtract($price2)->getAmount('EUR')
        );
    }

    public function testDividePrices()
    {
        $price1 = new Price();
        $price1->set('EUR', 10);
        $price1->set('USD', 20);

        $this->assertEquals(10,
            $price1->divide(2)->getAmount('USD')
        );
    }

    public function testMultiplyPrices()
    {
        $price1 = new Price();
        $price1->set('EUR', 10);
        $price1->set('USD', 20);

        $this->assertEquals(20,
            $price1->multiply(2)->getAmount('EUR')
        );
    }
}