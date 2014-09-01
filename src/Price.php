<?php

namespace Leaphly\Price;

use Money\CurrencyPair;
use Money\InvalidArgumentException;
use Money\Money;
use Money\Currency;

class Price implements \Iterator
{
    private $money       = array();
    private $conversions = array();

    /**
     * Construct a Price from money and conversions
     */
    public function __construct(array $money, array $conversions = null)
    {
        if ((count($money) <= 0) && null != $conversions) {
            throw new \InvalidArgumentException('Conversion should be null if no money are given.');
        }
        if (null !== $money) {
            $this->fromArray($money);
        }
        if (null !== $conversions) {
            $this->addConversions($conversions);
        }
    }

    /**
     * Get all conversions.
     *
     * @return CurrencyPair[]
     */
    public function getConversions()
    {
        return array_reduce($this->conversions, function (&$conversions, $conversionsForCurrency) {

            return array_merge(
                $conversions,
                array_values($conversionsForCurrency)
            );
        }, array());
    }

    /**
     * Magic call that helps adding money or retrieving amount
     *  eg  inEUR()
     *      getEUR() @deprecated
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
        if (($currency = $this->isMagicGetter($method)) !== false
            || ($currency = $this->isMagicIn($method))  !== false) {
            return $this->getAmount($currency);
        }

        throw new InvalidArgumentException();
    }

    /**
     * Is 0 if all the currencies has not a positive or negative number.
     *
     * @return bool
     */
    public function isZero()
    {
        $array = $this->toArray();

        foreach ($array as $money) {
            if (!$money->isZero()) {
                return false;
            }
        }

        return true;
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
     * @param  string|Currency $currency
     * @return bool
     */
    public function hasAmount($currency)
    {
        $currency = (string) $currency;

        return ($this->doGetMoney($currency));
    }

    /**
     * Get the Money object for a Given Currency,
     *  if it doesnt' find the currency it tries to convert using conversions.
     *
     * @param $currency
     *
     * @return Money|null
     */
    public function getMoney($currency)
    {
        $currency = (string) $currency;

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

        if ($other->getConversions() != $this->getConversions()) {
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
     * Set a money in the given currency and amount
     */
    private function set($currency, $value)
    {
        $currency = (string) $currency;
        $this->money[$currency] = new Money($value, new Currency($currency));

        return $this;
    }

    /**
     * Add a Conversion Pair into the Price
     *  eg. 'EUR/USD 1.2500'
     */
    private function addConversion($conversion)
    {
        if (is_string($conversion)) {
            $conversion = CurrencyPair::createFromIso($conversion);
        }

        // assert that the Counter currency doesn't exist as explicit currency
        if ($this->hasAmount($conversion->getCounterCurrency())) {
            throw new \InvalidArgumentException(sprintf(
                'Impossible to add as a counterCurrency %s,
                a currency that already exist in the explicit currency.',
                $conversion->getCounterCurrency()
            ));
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
        [(string) $conversion->getCounterCurrency()]
        [(string) $conversion->getBaseCurrency()] = $conversion;
    }

    /**
     * Detect the magic getter and returns the currency ISO string,
     * returns false otherwise.
     */
    private function isMagicGetter($method)
    {
        $currencyToGet = str_replace('get', '', $method, $count);

        return ($count > 0) ? $currencyToGet : false;
    }

    /**
     * Detect the magic in and returns the currency ISO string,
     * returns false otherwise.
     */
    private function isMagicIn($method)
    {
        $currencyToConvertIn = str_replace('in', '', $method, $count);

        return ($count > 0) ? $currencyToConvertIn : false;
    }

    private function doGetMoney($currency)
    {
        $currency = (string) $currency;

        if (!isset($this->money[$currency])) {
            return null;
        }

        return $this->money[$currency];
    }

    private function hasConversion($currency)
    {
        return isset($this->conversions[(string) $currency]);
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
            if ($amount instanceof Money) {
                $currency = (string) $amount->getCurrency();
                $amount = $amount->getAmount();
            }
            $this->set($currency, $amount);
        }
    }

    private function executeMoneyFunction($divOrMult, $amountFunction)
    {
        $array = $this->toArray();

        $currencies = array();
        foreach ($array as $currency => $money) {
            $result = $money->{$amountFunction}($divOrMult);
            $currencies[(string) $currency] = $result->getAmount();
        }

        return new Price($currencies, $this->getConversions());
    }

    private function executeMoneyFunctionOnPrice(Price $other, $amountFunction)
    {
        $newPriceCurrencies = array();
        $currencies = array_unique(array_merge($this->availableCurrencies(), $other->availableCurrencies()));

        foreach ($currencies as $currency) {
            $moneyA = new Money(0, new Currency($currency));
            $moneyB = new Money(0, new Currency($currency));

            if ($moneyTmp = $this->getMoney($currency)) {
                $moneyA = $moneyTmp;
            }
            if ($moneyTmp = $other->getMoney($currency)) {
                $moneyB = $moneyTmp;
            }

            $result = $moneyA->{$amountFunction}($moneyB);
            $newPriceCurrencies[(string) $currency] = $result->getAmount();
        }

        $conversions = $this->removeConversionWithCounterCurrencyInCurrencies(
            array_merge($this->getConversions(), $other->getConversions()),
            array_keys($newPriceCurrencies)
        );

        return new Price($newPriceCurrencies, $conversions);
    }

    private function removeConversionWithCounterCurrencyInCurrencies(array $conversions, array $currencies)
    {
        $conversionsFiltered = array();
        foreach ($conversions as $conversion) {
            if (!in_array((string) $conversion->getCounterCurrency(), $currencies)) {
                $conversionsFiltered[] = $conversion;
            }
        }

        return $conversionsFiltered;
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
