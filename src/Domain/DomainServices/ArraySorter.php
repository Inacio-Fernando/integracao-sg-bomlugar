<?php

namespace IntegracaoSgsistemas\Domain\DomainServices;

class ArraySorter
{
	static function sort(&$data)
	{
		try {
			$colunaFilial  = array_column($data, 'NROEMPRESA');
			$colunaLevePague = array_column($data, 'SEQPROMOCAOLEVEPAGUE');
			$colunaTipoItem = array_column($data, 'TIPOITEMPROMOC');
			array_multisort($colunaFilial, SORT_ASC, $colunaLevePague, SORT_ASC, $colunaTipoItem, SORT_DESC, $data);
		} catch (\Exception $e) {
			echo nl2br("\n");
			echo "Failed to sort array";
			echo nl2br("\n");
		}
	}
}
