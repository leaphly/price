<?php

namespace Leaphly\Price;

use Leaphly\Price\Price;

class ExplicitPriceTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $price = new Price(
            array(
                'EUR' => 10,
                'USD' => 120,
                'GBP' => 12,
            )
        );

        $this->assertInstanceOf(
            'Leaphly\Price\Price',
            $price
        );

        $this->assertEquals(10,
            $price->getAmount('EUR')
        );
    }

    public function testIsZeroOnEmptyPrice()
    {
        $price = new Price();

        $this->assertTrue($price->isZero());
    }

    public function testIsZeroOnMultipleZeroCurrencies()
    {
        $price = new Price(
            array(
                'EUR' => 0,
                'USD' => 0,
                'GBP' => 0,
            )
        );

        $this->assertTrue($price->isZero());
    }

    public function testMagicCallSetAndGet()
    {
        $price = new Price();
        $price->setEUR(20);

        $this->assertInstanceOf(
            'Leaphly\Price\Price',
            $price
        );

        $this->assertEquals($price->getAmount('EUR'), $price->getEUR(20));
    }



    public function testSetEur()
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

    public function testAddDisjoined()
    {
        $price1 = new Price();
        $price1->set('USD', 10);
        $price1->set('EUR', 20);
        $price1->set('GBP', 30);

        $price2 = new Price();
        $price2->set('USD', 1);
        $price2->set('EUR', 2);
        $price2->set('CHF', 3);

        $price1 = $price1->subtract($price2);

        $this->assertEquals(
            18,
            $price1->getAmount('EUR')
        );

        $this->assertEquals(
            9,
            $price1->getAmount('USD')
        );

        $this->assertEquals(
            -3,
            $price1->getAmount('CHF')
        );


        $this->assertEquals(
            30,
            $price1->getAmount('GBP')
        );
    }


    public function testAdd()
    {
        $price1 = new Price();
        $price1->set('USD', 10);
        $price1->set('EUR', 20);
        $price1->set('GBP', 30);

        $price2 = new Price();
        $price2->set('USD', 1);
        $price2->set('EUR', 2);
        $price2->set('CHF', 3);

        $price1 = $price1->add($price2);

        $this->assertEquals(
            22,
            $price1->getAmount('EUR')
        );

        $this->assertEquals(
            11,
            $price1->getAmount('USD')
        );

        $this->assertEquals(
            3,
            $price1->getAmount('CHF')
        );


        $this->assertEquals(
            30,
            $price1->getAmount('GBP')
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