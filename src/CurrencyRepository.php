<?php
namespace Jalno\LaravelMoney;

use Jalno\LaravelMoney\Models\Currency;
use Jalno\Money\Contracts\ICurrency;
use Jalno\Money\Contracts\ICurrencyRepository;
use Jalno\Money\Contracts\ICurrency\Expression;
use Jalno\Money\Exception\CurrencyRepositorySaveException;

class CurrencyRepository implements ICurrencyRepository
{

	public function getByID(int $id): ?ICurrency
	{
		return (new Currency)->firstWhere("id", $id);
	}

	/**
	 * @return ICurrency[]
	 */
	public function byCode(string $code): array
	{
		return (new Currency)->where("code", $code)->get()->toArray();
	}

	public function save(ICurrency $currency): void
	{
		if ($currency instanceof Currency)
		{
			$result = $currency->save();
			if (!$result)
			{
				throw new CurrencyRepositorySaveException(
					"can not save into database",
					$this,
					$currency
				);
			}
			return;
		}

		$model = (new Currency)->firstWhere("id", $currency->getID());
		if (!$model)
		{
			$model = new Currency();
		}

		if ($model->getCode() !== $currency->getCode())
		{
			$model->setCode($currency->getCode());
		}

		$prefixes = $currency->getPrefixes();
		foreach ($prefixes as $prefix)
		{
			$model->setPrefix(new Expression($prefix->getValue(), $prefix->getLanguage(), $prefix->getLocale()));
		}

		$postfixes = $currency->getPostfixes();
		foreach ($postfixes as $postfix)
		{
			$model->setPostfix(new Expression($postfix->getValue(), $postfix->getLanguage(), $postfix->getLocale()));
		}

		$result = $model->save();
		if (!$result)
		{
			throw new CurrencyRepositorySaveException(
				"can not save into database",
				$this,
				$model
			);
		}
	}
}