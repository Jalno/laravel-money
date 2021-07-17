<?php
namespace Jalno\LaravelMoney;

use Jalno\Money\Contracts\ICurrency;
use Jalno\LaravelMoney\Models\ExchangeRate;
use Jalno\Money\Contracts\IExchangeRateProvider;
use Jalno\Money\Exception\CurrencyConversionException;

class ExchangeRateProvider implements IExchangeRateProvider
{
    /**
     * @param ICurrency $baseCurrency The source currency code.
     * @param ICurrency $quoteCurrency The target currency code.
     *
     * @return int|float|string The exchange rate.
     *
     * @throws CurrencyConversionException If the exchange rate is not available.
     */
    public function getExchangeRate(ICurrency $baseCurrency, ICurrency $quoteCurrency)
    {
        $baseID = $baseCurrency->getID();
        $quoteID = $quoteCurrency->getID();
        $rate = (new ExchangeRate)->where("base_currency_id", $baseID)->where("quote_currency_id", $quoteID)->first();

        if (empty($rate))
        {
            throw new CurrencyConversionException(
                "can not find rate for currency pair",
                $baseCurrency,
                $quoteCurrency
            );
        }

        return $rate->ratio;
    }
}
