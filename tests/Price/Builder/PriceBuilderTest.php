<?php
/**
 * This file is part of price package.
 *
 * Simone Di Maulo <toretto460@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leaphly\Price\Builder;


class PriceBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Get a new PriceBuilder instance.
     *
     * @return PriceBuilder
     */
    private function getABuilderInstance()
    {
        return new PriceBuilder();
    }

    /**
     * @test
     */
    public function itShouldThrowAnExceptionOnEmptyPriceWithConversion()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->getABuilderInstance()
            ->addConversion('EUR/USD 1.3400')
            ->build();
    }

    /**
     * @test
     * @dataProvider provideMoney
     *
     * @param $moneys
     * @param $testCurrency
     * @param $testValue
     */
    public function itShoudCreateAConsistentPrice($moneys, $testCurrency, $testValue)
    {
        $builder = $this->getABuilderInstance();

        foreach($moneys as $currency => $value) {

            $builder->addValue($currency, $value);
        }
        $price = $builder->build();

        $this->assertEquals($testValue, $price->{'in'.$testCurrency}());
    }

    public function provideMoney()
    {
        return [
            '10 EUR' => [['EUR' => 10, 'GBP' => 12], 'EUR', 10],
            '2 GBP'  => [['GBP' => 2], 'GBP', 2]
        ];
    }

}