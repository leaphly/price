Price
=====

[![Build Status](https://secure.travis-ci.org/leaphly/price.png?branch=master)](http://travis-ci.org/leaphly/price)
[![Total Downloads](https://poser.pugx.org/leaphly/price/downloads.png)](https://packagist.org/packages/leaphly/price)
[![Latest Stable Version](https://poser.pugx.org/leaphly/price/v/stable.png)](https://packagist.org/packages/leaphly/price)

### A Price is the amount of a product in different currencies.

```
In Dollar a t-shirt     costs 4$,
in Eur the same t-shirt costs 3€
if British Pound the cost is given by the current conversion of 0.7900
```

in PHP is:

```php
$tShirtPrice = new Price(
  [
    'EUR' => 300,
    'USD' => 400
  ],
  ['EUR/GBP 0.7900'] // array of conversions
);

echo $tShirtPrice->getEUR(); // 300  same as ->getAmount('EUR')
echo $tShirtPrice->getUSD(); // 400  same as ->getAmount('USD')
echo $tShirtPrice->getGBP(); // 237  same as ->getAmount('GBP')
```

### Why!!

- Because is not recommended to work with the float for the money in PHP..
- Because is better to implement money as value objects.
- Because in the e-commerce domain a product has always* a different price for different currencies.
- Because we needed :).

### Goodies:

- It helps you to work with money.
- It helps you to work with currencies.
- It helps you to work with multiple currencies, converted or explicit.
- It is shipped with some math operations: `addition`, `multiplication`, `division`, `subtraction` ...
- This library extends the [mathiasverraes/money](https://packagist.org/packages/mathiasverraes/money).

### Simple usage

* The T-Shirt costs 10€ or 8£

#### Constructor

Usage with explicit currency values.

```php
$ticketPrice = new Price(
  [
    'EUR' => 1000,
    'GBP' => 800
  ]
);

echo $tShirtPrice->getEUR();  // return 1000

var_dump($ticketPrice->availableCurrencies()); // array with EUR, GBP
```

#### Usage with mixed explicit and converted values

```php
$ticketPrice = new Price(
  [
    'EUR' => 100,
    'USD' => 130
  ],
  ['EUR/GBP 0.7901'] // this is an array of conversions
);

echo $ticketPrice->getAmount('EUR'); // 100
echo $ticketPrice->getAmount('GBP'); // 79 is calculated

var_dump($ticketPrice->availableCurrencies()); // array with EUR, USD, GBP
```

### Do we use the same language?

* An espresso coffee costs [2€ or 2.3$] here and [1€ or 1.2$] take away.

`espresso` is a product.

`here` and `take away` are contexts (*still is a missing feature*).

`2€` `2.3£` is a Price with 2 currencies,

`1€` `1.2£` is a Price with 2 currencies,

`2€ or 2.3$ here, and 1€ or 1.2$ for take away.` is a PriceList (*still is a missing feature*).


API (still not stable)
----------------------

### Price

```php
    public function set($currency, $value);

    public function getABC($currency); // ABC is a currency like EUR

    public function getAmount($currency);

    public function hasAmount($currency);

    public function availableCurrencies();

    public function equals(Price $other);

    public function add(Price $addend);

    public function subtract(Price $subtrahend);

    public function multiply($multiplier);

    public function divide($divisor);
```

#### Example sum two prices


```php
$ticketPrice = new Price(
  [
    'EUR' => 100,
    'USD' => 130
  ],
  ['EUR/GBP 0.7901'] // this is an array of conversions
);

$shirtPrice = new Price(
  [
    'EUR' => 200,
    'CHF' => 300,
    'GBP' => 400
  ],
);

// sum
$sumPrice = $ticketPrice->add($shirtPrice);

$sumPrice->getEUR(); // 100+200= 400
$sumPrice->getGBP(); //  79+400= 479
$sumPrice->getUSD(); //          130
$sumPrice->getCHF(); //          300
```

License [![License](https://poser.pugx.org/leaphly/price/license.png)](https://packagist.org/packages/leaphly/price)
-------

This library is under the MIT license. See the complete license in the repository:

    Resources/meta/LICENSE

Test
----

``` bash
composer.phar create-project leaphly/price dev-master`
bin/phpunit
```

About
-----

See also the list of [contributors](https://github.com/leaphly/price/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/leaphly/price/issues).