<?php

namespace Leaphly\Price\Infrastructure;

use Leaphly\Price\Price;
use Money\Money;
use Money\CurrencyPair;

class PricePresentation
{
    public static function stringifyPrice(Price $price)
    {
        $moneyArray = $price->toArray();
        array_walk($moneyArray, function (Money &$money) {
                $money = (string) $money->getCurrency() . ' '. $money->getAmount();

                return $money;
            });

        $conversionsArray =  $price->getConversions();
        array_walk($conversionsArray, function (CurrencyPair &$conversion) {
                $conversion = (string) $conversion->getBaseCurrency().'/'.(string) $conversion->getCounterCurrency().' '.$conversion->getRatio();

                return $conversion;
            });

        $output = implode(',', $moneyArray);

        if (count($conversionsArray)>0) {
            $output .= ';'.implode(',', $conversionsArray);
        }

        return $output;
    }

    public static function createPriceFromString($string)
    {
        $money = $string;
        $conversions = null;
        if (strpos($string, ';')>0) {
            list($money, $conversions) = explode(';', $string, 2);
        }

        $moneys = explode(',', $money);
        $arrayOfMoney = array();
        foreach ($moneys as $moneyString) {
            list($currency, $amount) = explode(' ', $moneyString);
            $arrayOfMoney[$currency]= (int) $amount;
        }

        if (null !== $conversions) {
            $conversions = explode(',', $conversions);
            if (!is_array($conversions) || count($conversions)<=0) {
                $conversions = null;
            }
        }

        return new Price($arrayOfMoney, $conversions);
    }
}
