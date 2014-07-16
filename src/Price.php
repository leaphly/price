<?php

namespace Leaphly\Price;

use Money\CurrencyPair;
use Money\InvalidArgumentException;
use Money\Money;
use Money\Currency;

class Price implements \Iterator
{
    const UNION     = 1; // add all values on both sets.
    const INTERSECT = 1; // add only the values that are on both sets.

    private $money;
    private $conversions;

    public function __construct(array $money = null, array $conversions = null)
    {
        // validate input, 2 input or error.
        $this->money = array();
        $this->conversions = array();

        if (null !== $money) {
            $this->fromArray($money);
        }
        if (null !== $conversions) {
            $this->addConversions($conversions);
        }
    }

    /**
     * @param string|CurrencyPair $conversion
     *
     * @throws \Exception
     */
    public function addConversion($conversion)
    {
        if (is_string($conversion)) {
            $conversion = CurrencyPair::createFromIso($conversion);
        }

        // assert the BaseCurrency exists or is calculable
        if (!$this->hasAmount($conversion->getBaseCurrency())
            && !isset($this->conversions[(string) $conversion->getBaseCurrency()])
        )
        {
            throw new \Exception(sprintf('Impossible to find %s, in the array of possible conversion,
             be careful the order is important.', $conversion->getBaseCurrency()));
        }

        $this->conversions
            [(string) $conversion->getCounterCurrency()]
            [(string) $conversion->getBaseCurrency()] = $conversion;
    }

    public function __call($method, $args)
    {
        $currency = str_replace('set', '', $method, $count);

        if ($count > 0) {
            return $this->set($currency, $args[0]);
        }

        throw new InvalidArgumentException();
    }

    /**
     * set a money in the given Amount
     *
     * @param string $currency eg 'EUR'
     * @param int    $value    eg. 100
     *
     * @return $this
     */
    public function set($currency, $value)
    {
       $currency = (string) $currency;
       $this->money[$currency] = new Money($value, new Currency($currency));

       return $this;
    }

    public function getAmount($currency)
    {
        return $this->getMoney($currency)->getAmount();
    }

    public function hasAmount($currency)
    {
        $currency = (string) $currency;

        return isset($this->money[$currency]);
    }

    public function getMoney($currency)
    {
        $currency = (string) $currency;

        if (!$this->hasAmount($currency)) {
            return $this->calculateConversion($currency);
        }

        return $this->money[$currency];
    }

    /**
     * Return an array of all the currencies, that is possible fetch the amount.
     *
     * @return array
     */
    public function availableCurrencies()
    {
        return array_merge(array_keys($this->money), array_keys($this->conversions));
    }

    /**
     * Add another price to the actual, the result is the intersection.
     *
     * @param Price $addend
     *
     * @return Price
     */
    public function add(Price $addend)
    {
        return $this->executeMoneyFunctionOnPrice($addend, __FUNCTION__);
    }

    public function equals(Price $other)
    {
        if ($other->toArray() != $this->toArray()) {
            return false;
        }

        return true;
    }

    /**
     * Subtract another price to the actual, works only for the same currencies,
     * the result is the intersection of currencies.
     *
     * @param Price $subtrahend
     *
     * @return Price
     */
    public function subtract(Price $subtrahend)
    {
        return $this->executeMoneyFunctionOnPrice($subtrahend, __FUNCTION__);
    }

    /**
     * multiply another price to the actual, works only for the same currencies,
     * the result is the intersection of currencies.
     *
     * @param int|float $multiplier
     *
     * @return Price
     */
    public function multiply($multiplier)
    {
        return $this->executeMoneyFunction($multiplier, __FUNCTION__);
    }

    /**
     * divide another price to the actual, works only for the same currencies,
     * the result is the intersection of currencies.
     *
     * @param int|float $divisor
     *
     * @return Price
     */
    public function divide($divisor)
    {
        return $this->executeMoneyFunction($divisor, __FUNCTION__);
    }

    private function calculateConversion($currency)
    {
        // I need the amount for this currency.
        $conversion = reset($this->conversions[(string) $currency]);

        $moneyBase = $this->getMoney($conversion->getBaseCurrency());

        $moneyConverted = $conversion->convert($moneyBase);

        return $moneyConverted;
    }

    private function addConversions($conversions)
    {
        foreach ($conversions as $conversion) {
            $this->addConversion($conversion);
        }
    }

    private function fromArray($array)
    {
        foreach ($array as $currency => $amount) {
            $this->set($currency, $amount);
        }
    }

    private function executeMoneyFunction($divOrMult, $amountFunction)
    {
        $newPrice =  new Price();
        $array = $this->toArray();

        foreach ($array as $currency => $money) {
            $result = $money->{$amountFunction}($divOrMult);
            $newPrice->set($result->getCurrency(), $result->getAmount());
        }

        return $newPrice;
    }

    private function executeMoneyFunctionOnPrice(Price $other, $amountFunction)
    {
        $newPrice =  new Price();
        $array = $this->toArray();

        foreach ($array as $currency => $money) {
            if ($other->hasAmount($currency)) {
                $result = $money->{$amountFunction}($other->getMoney($currency));
                $newPrice->set($result->getCurrency(), $result->getAmount());
            }
        }

        return $newPrice;
    }

    public function toArray()
    {
        return $this->money;
    }

    /*
    Iterator
    */
    public function rewind()
    {
        return reset($this->money);
    }
    public function current()
    {
        return current($this->money);
    }
    public function key()
    {
        return key($this->money);
    }
    public function next()
    {
        return next($this->money);
    }
    public function valid()
    {
        return key($this->money) !== null;
    }

}
