<?php

namespace IntegracaoSgsistemas\Application\Usecases\Strategy;

use IntegracaoSgsistemas\Application\Gateways\ApiGateway;
use IntegracaoSgsistemas\Domain\DomainServices\DumpLoader;
use IntegracaoSgsistemas\Domain\DomainServices\ValueFormatter;

class DefaultPriceCreationStrategy implements PriceCreationStrategy {
    private $gateway;
    public function __construct(ApiGateway $gateway)
    {
        $this->gateway = $gateway;        
    }

    public function getPricesData() : array {
        // $data =$this->gateway->getPrices();
        $data = DumpLoader::load('dump-precos.json');
        return $data;
    }
    public function getValueAndDynamic($product): array {
        $regularPrice = ($product['precoVenda'] ?? 0);
        
        $value = ValueFormatter::formatNumber($regularPrice);
        $vlr_id_comercial = 1;

		return [$value, $vlr_id_comercial];
    }
}