<?php

namespace Leaphly\Price;

use Money\CurrencyPair;
use Money\InvalidArgumentException;
use Money\Money;
use Money\Currency;

class Price implements \Iterator
{
    private $money;
    private $conversions;

    public function __construct(array $money = null, array $conversions = null)
    {
        if ((null == $money || count($money) <= 0) && null != $conversions) {
            throw new \InvalidArgumentException('Conversion should be null if no money are given.');
        }

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
     * Add a Conversion Pair into the Price
     *  eg. 'EUR/USD 1.2500'
     *
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
            && !$this->hasConversion($conversion->getBaseCurrency())
        ) {
            throw new \Exception(sprintf(
                'Impossible to find %s, in the array of possible conversion,
                             be careful the order is important.',
                $conversion->getBaseCurrency()
            ));
        }

        $this->conversions
        [(string)$conversion->getCounterCurrency()]
        [(string)$conversion->getBaseCurrency()] = $conversion;
    }

    public function getConversions()
    {
        $array = array();
        foreach ($this->conversions as $currency => $conversions)
        {
            foreach ($conversions as $currency => $conversion)
            {
                $array[] = $conversion;
            }
        }

        return $array;
    }

    /**
     * Magic call that helps adding money or retrieving amount
     *  eg. setEUR(10)
     *      getEUR(10)
     *
     * @param $method
     * @param $args
     *
     * @return $this
     *
     * @throws \Money\InvalidArgumentException
     */
    public function __call($method, $args)
    {
        $currency = str_replace('set', '', $method, $count);

        if ($count > 0) {
            return $this->set($currency, $args[0]);
        }

        $count = 0;
        $currency = str_replace('get', '', $method, $count);

        if ($count > 0) {
            return $this->getAmount($currency);
        }


        throw new InvalidArgumentException();
    }

    /**
     * Set a money in the given currency and amount
     *
     * @param string|Currency $currency eg 'EUR'
     * @param int             $value eg. 100
     *
     * @return $this
     */
    public function set($currency, $value)
    {
        $currency = (string)$currency;
        $this->money[$currency] = new Money($value, new Currency($currency));

        return $this;
    }

    /**
     * Return the int amount for a currency, if exists.
     *
     * @param string|Currency $currency
     *
     * @return int
     */
    public function getAmount($currency)
    {
        return $this->getMoney($currency)->getAmount();
    }

    /**
     * True if this price has an amount for that currency.
     *
     * @param string|Currency $currency
     * @return bool
     */
    public function hasAmount($currency)
    {
        $currency = (string)$currency;

        return ($this->doGetMoney($currency));
    }

    /**
     * Get the Money object for a Given Currency,
     *  if it doesnt' find the currency it tries to convert using conversions.
     *
     * @param $currency
     * @return null
     */
    public function getMoney($currency)
    {
        $currency = (string)$currency;

        if (!$this->hasAmount($currency)
            && $this->hasConversion($currency)) {
            return $this->calculateConversion($currency);
        }

        return $this->doGetMoney($currency);
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
     * Add another price to the actual, the result is the intersection, the conversions are not modified.
     *
     * @param Price $addend
     *
     * @return Price
     */
    public function add(Price $addend)
    {
        return $this->executeMoneyFunctionOnPrice($addend, __FUNCTION__);
    }

    /**
     * Return true if the this prices has the same amounts, conversions are not compared.
     *
     * @param Price $other
     *
     * @return bool
     */
    public function equals(Price $other)
    {
        if ($other->toArray() != $this->toArray()) {
            return false;
        }

        return true;
    }

    /**
     * Subtract another price to the actual, works only for the same currencies,
     * the result is the intersection of currencies, the conversions are not modified.
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
     * the result is the intersection of currencies, the conversions are not modified.
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
     * the result is the intersection of currencies, the conversions are not modified.
     *
     * @param int|float $divisor
     *
     * @return Price
     */
    public function divide($divisor)
    {
        return $this->executeMoneyFunction($divisor, __FUNCTION__);
    }

    private function doGetMoney($currency)
    {
        $currency = (string)$currency;

        if (!isset($this->money[$currency])) {
            return null;
        }

        return $this->money[$currency];
    }

    private function hasConversion($currency)
    {
        return isset($this->conversions[(string)$currency]);
    }

    private function calculateConversion($currency)
    {
        // I need the amount for this currency.
        $conversion = reset($this->conversions[(string)$currency]);

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
        $newPrice = new Price();
        $array = $this->toArray();

        foreach ($array as $currency => $money) {
            $result = $money->{$amountFunction}($divOrMult);
            $newPrice->set($result->getCurrency(), $result->getAmount());
        }

        return $newPrice;
    }

    private function executeMoneyFunctionOnPrice(Price $other, $amountFunction)
    {
        $newPrice = new Price();
        $currencies = array_merge($this->availableCurrencies(), $other->availableCurrencies());

        foreach ($currencies as $currency) {
            $moneyA = new Money(0, new Currency($currency));
            $moneyB = new Money(0, new Currency($currency));

            if ($this->hasAmount($currency)) {
                $moneyA = $this->getMoney($currency);
            }
            if ($other->hasAmount($currency)) {
                $moneyB = $other->getMoney($currency);
            }

            $result = $moneyA->{$amountFunction}($moneyB);
            $newPrice->set($result->getCurrency(), $result->getAmount());
        }
        $newPrice->addConversions($this->getConversions());
        $newPrice->addConversions($other->getConversions());

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
