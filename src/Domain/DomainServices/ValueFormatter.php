<?php

namespace IntegracaoSgsistemas\Domain\DomainServices;


class ValueFormatter
{
	static function format(string $price)
	{
		$number = floatval(preg_replace(['/[.]([0-9]{3,})/', '/[\.\,]([0-9]{1,2}$)/'], ['$1', '.$1'], $price));
		$price = number_format(floatval($number), 2, ',', '.');
		return $price;
	}

	static function formatString(string $string)
	{
		return strtolower(preg_replace('/\s+/', '', $string));
	}
	static function formatNumber(string $price)
	{
        return number_format($price, 2, ",","");
    }
}
