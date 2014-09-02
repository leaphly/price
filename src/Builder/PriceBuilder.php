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

use Leaphly\Price\Price;
use Money\Currency;
use Money\Money;

class PriceBuilder
{
    private $money       = [];
    private $conversions = [];

    /**
     * @param String  $currency
     * @param Integer $moneyValue
     *
     * @return self
     */
    public function addValue($currency, $moneyValue)
    {
        $this->money[$currency] = $moneyValue;

        return $this;
    }

    /**
     * @param String $conversion
     *
     * @return self
     */
    public function addConversion($conversion)
    {
        $this->conversions[md5($conversion)] = $conversion;

        return $this;
    }

    /**
     * @return Price
     */
    public function build()
    {
        return new Price($this->money, $this->conversions);
    }
}