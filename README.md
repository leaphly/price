Price
=====

## Unstable version things will change do not use and help :)

[![Build Status](https://secure.travis-ci.org/leaphly/price.png?branch=master)](http://travis-ci.org/leaphly/price)
[![Total Downloads](https://poser.pugx.org/leaphly/price/downloads.png)](https://packagist.org/packages/leaphly/price)
[![Latest Stable Version](https://poser.pugx.org/leaphly/price/v/stable.png)](https://packagist.org/packages/leaphly/price)

#### Usually a product has a price in different currencies.
#### Price is a set of money in a given currency.
#### A PriceList is a set of Price in a given context *still missing feature*


### Price usage

* The T-Shirt costs 10€ or 8£

*Constructor*

```php
$ticketPrice = new Price(
  [
    'EUR' => 10,
    'USD' => 11,
    'GBP' => 12,
  ]
);

// or

$ticketPrice = new Price();

$ticketPrice->setEUR(1000);
$ticketPrice->setGBP(800);
$ticketPrice->setCHF(800);
```

*Usage with explicit currency value*

```php
$ticketPrice = new Price(
  [
    'EUR' => 10,
    'USD' => 11,
    'GBP' => 12,
  ]
);

echo $ticketPrice->getAmount('EUR'); // 10
echo $ticketPrice->getAmount('USD'); // 11
```

*Usage with conversion ratio, the value of another currency is calculated*

```php
$ticketPrice = new Price(
  ['EUR' => 100],    // array of money
  ['EUR/USD 1.2500'] // array of conversions
);

echo $ticketPrice->getAmount('EUR'); // 100
echo $ticketPrice->getAmount('USD'); // 125
```


### Price language

* An espresso coffee costs [2€ or 2.3$] here and [1€ or 1.2$] take away.

`espresso` is a product.

`here` and `take away` are Contexts.

`2€` `2.3£` is a Price with 2 currencies,

`1€` `1.2£` is a Price with 2 currencies,

`2€ or 2.3$ here, and 1€ or 1.2$ for take away.` is a PriceList.

There's a working example here [ExampleTest.php](./tests/Price/ExampleTest.php)

API
---

### Price

```php

    public function equals(Price);

    public function set($currency, $value)

    public function add(Price $addend)

    public function subtract(Price $subtrahend)

    public function multiply($multiplier, $rounding_mode = Money::ROUND_HALF_UP)

    public function divide($divisor, $rounding_mode = Money::ROUND_HALF_UP)
```

This library uses the mathiasverraes/money.

License [![License](https://poser.pugx.org/leaphly/price/license.png)](https://packagist.org/packages/leaphly/price)
-------

This library is under the MIT license. See the complete license in the repository:

    Resources/meta/LICENSE

Test
----

``` bash
composer.phar create-project leaphly/price ~1`
bin/phpunit
```

About
-----

See also the list of [contributors](https://github.com/leaphly/price/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/leaphly/price/issues).