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

echo $tShirtPrice->inEUR(); // 300  same as ->getAmount('EUR')
echo $tShirtPrice->inUSD(); // 400  same as ->getAmount('USD')
echo $tShirtPrice->inGBP(); // 237  same as ->getAmount('GBP')
```

### Why?!

- Because is not recommended to work with the float for the money in PHP...
- Because is better to implement money as value objects.
- Because in the e-commerce domain a product has always* a different price for different currencies.
- Because we needed :).

### Goodies:

- It helps you to work with money.
- It helps you to work with currencies.
- It helps you to work with multiple currencies, converted or explicit.
- It is shipped with some math operations: `addition`, `multiplication`, `division`, `subtraction` ...
- This library extends the [mathiasverraes/money](https://packagist.org/packages/mathiasverraes/money).
- Immutable Value Object.
- Shipped with an example of `DoctrineType`

### Simple usage

* The T-Shirt costs 10€ and 8£

#### Constructor

Usage with explicit currency values.

```php
$ticketPrice = new Price(
  [
    'EUR' => 1000,
    'GBP' => 800
  ]
);

echo $ticketPrice->inEUR();  // return 1000

var_dump($ticketPrice->availableCurrencies()); // array with EUR, GBP
```


#### Usage with mixed explicit and converted values

```php
$ticketPrice = new Price(
  [
    'EUR' => 100,
    'USD' => 130
  ],
  ['EUR/GBP 0.7901'] // this is an array of conversions with the ISO standard format.
);

echo $ticketPrice->inEUR(); // 100
echo $ticketPrice->inGBP(); // 79 is calculated

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
    public function inXYZ($currency); // ZYX is a valid currency like EUR or GBP

    public function getAmount($currency);

    public function hasAmount($currency);

    public function availableCurrencies();

    public function equals(Price $other);

    public function add(Price $addend);

    public function subtract(Price $subtrahend);

    public function multiply($multiplier);

    public function divide($divisor);

    public function isZero();
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

$sumPrice->inEUR(); // 100+200= 400
$sumPrice->inGBP(); //  79+400= 479
$sumPrice->inUSD(); //          130
$sumPrice->inCHF(); //          300
```

#### With the \Iterator interface

Implement the `\Iterator` so Price is an array of Money.

``` php
$price = new Price ....
foreach ($price as $money) {
    echo $money->getAmount() . ' in '. $money->getCurrencies();
}
```

#### Use it with the Money Value Object


``` php
use Money\Money;
use Money\CurrencyPair;

$price = new Price(
    array(
        Money::EUR(5),
        Money::USD(10),
        Money::GBP(10),
        'TRY' => 120  // or mixed
    ),
    [
        CurrencyPair::createFromIso('USD/CHF 1.5'),
    ]
);
```

Note: the iteration is valid only on the explicit currencies not on the converted one.

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

Note: this library uses the `dev` version of the [Mathias Verraes Money](https://github.com/mathiasverraes/money/).
