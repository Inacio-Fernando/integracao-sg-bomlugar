<?php

namespace IntegracaoSgsistemas\Domain\DomainServices;

use IntegracaoSgsistemas\Infra\Log\Log;

class DumpLoader
{
	static function load($file_name, $log = null, $error = null)
	{

		$dump = file_get_contents(__DIR__ . "/../../../dumps/$file_name");


		if (!$dump) {
			Log::error("Dump '$file_name' não encontrado");
			return [];
		}

		$response = json_decode($dump, true);

		if (!$response || count($response) <= 0) {
			Log::error("Dump '$file_name' não contém dados");
            return [];
		}

		return $response;
	}
}
