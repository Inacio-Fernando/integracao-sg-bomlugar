<?php

namespace IntegracaoSgsistemas\Application\Usecases;

use IntegracaoSgsistemas\Application\Gateways\ApiGateway;
use IntegracaoSgsistemas\Application\Handler\AbstractHandler;
use IntegracaoSgsistemas\Application\Repositories\RulesRepository;
use IntegracaoSgsistemas\Domain\DomainServices\DumpLoader;
use IntegracaoSgsistemas\Infra\Database\Models\ProductTable;
use IntegracaoSgsistemas\Infra\Log\Log;
use Mavinoo\Batch\BatchFacade;
use Throwable;



class CreatePromotionalPricesUsecase implements Usecase 
{
    private $gateway;
    private $handler;
    public function __construct(ApiGateway $gateway, AbstractHandler $handler)
    {
        $this->gateway = $gateway;
        $this->handler = $handler;
    }
    function execute()
    {
      
        // $filiais = $this->gateway->getAllFiliais();
        // $filiais = array_column($filiais, 'id');
        $filiais = [35, 36, 37];
        $data = [];

        foreach ($filiais as $filial) {
            $promotionsData = $this->gateway->getPromotionsByFilial($filial);
            $data['promotions'] = $promotionsData;
            $data['filial'] = $filial;
            $this->handler->handle($data);
        }
    }
}
