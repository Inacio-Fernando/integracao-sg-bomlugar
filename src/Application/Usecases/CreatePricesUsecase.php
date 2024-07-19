<?php

namespace IntegracaoSgsistemas\Application\Usecases;

use IntegracaoSgsistemas\Application\Gateways\ApiGateway;
use IntegracaoSgsistemas\Application\Handler\AbstractHandler;
use IntegracaoSgsistemas\Application\Repositories\RulesRepository;
use IntegracaoSgsistemas\Application\Usecases\Strategy\PriceCreationStrategy;
use IntegracaoSgsistemas\Domain\DomainServices\DumpLoader;
use IntegracaoSgsistemas\Domain\DomainServices\ValueFormatter;
use IntegracaoSgsistemas\Infra\Database\Models\ProductTable;
use IntegracaoSgsistemas\Infra\Database\Models\TabelaValor;
use IntegracaoSgsistemas\Infra\Log\Log;
use Throwable;

class CreatePricesUsecase implements Usecase
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
            $pricesData = $this->gateway->getPricesByFilial($filial);
            $data['prices'] = $pricesData;
            $data['filial'] = $filial;
            $this->handler->handle($data);
        }
    }
}