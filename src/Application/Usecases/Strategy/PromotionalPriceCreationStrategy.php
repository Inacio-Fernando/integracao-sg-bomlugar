<?php

namespace IntegracaoSgsistemas\Application\Usecases\Strategy;

use Exception;
use IntegracaoSgsistemas\Domain\DomainServices\DumpLoader;
use IntegracaoSgsistemas\Domain\DomainServices\ValueFormatter;

class PromotionalPriceCreationStrategy implements PriceCreationStrategy {
    private $gateway;
    public function __construct(ApiGateway $gateway)
    {
        $this->gateway = $gateway;        
    }

    public function getPricesData() : array {
        //$data =$this->gateway->getPromotions();
        $data = DumpLoader::load('dump-promocao.json');
        return $data;
    }
    public function getValueAndDynamic($product): array {
        $regularPrice       = ValueFormatter::formatNumber($product['PRECO_BASE'] ?? 0);
        $promotionalPrice   = ValueFormatter::formatNumber($product['PRECO_PROMOCIONAL'] ?? 0);
        $clubPrice          = ValueFormatter::formatNumber($product['PRECO_MASCOTE'] ?? 0 );
        
        if ($regularPrice <= 0 && $promotionalPrice <= 0 && $clubPrice <= 0) {
            throw new Exception('Create Promotions: Os preços são todos menores ou iguais a 0');
        }

        if($clubPrice > 0) {
            $value = $promotionalPrice . "!@#" . $clubPrice;
            $vlr_id_comercial = 2; 
            return [$value, $vlr_id_comercial];
        }
        $value = $promotionalPrice > 0 ? $promotionalPrice : $regularPrice;
        $vlr_id_comercial = 1; 
        return [$value, $vlr_id_comercial];
    }
}