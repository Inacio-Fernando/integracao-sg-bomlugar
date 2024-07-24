<?php
require('vendor/autoload.php');

use IntegracaoSgsistemas\Application\Usecases\CreatePromotionalPricesUsecase;
use IntegracaoSgsistemas\Application\Handler\CreatePricesHandler;
use IntegracaoSgsistemas\Application\Handler\CreateProductsHandler;
use IntegracaoSgsistemas\Application\Usecases\CreatePricesUsecase;
use IntegracaoSgsistemas\Infra\Cron\CronHandlerImpl;

use IntegracaoSgsistemas\Infra\Gateways\ApiGatewayHttp;
use IntegracaoSgsistemas\Infra\Http\GuzzleAdapter;
use IntegracaoSgsistemas\Application\Usecases\CreateProductsUsecase;
use IntegracaoSgsistemas\Infra\Cron\CronController;
use IntegracaoSgsistemas\Infra\Database\Database;


try {
    Database::setupEloquent();
    $client = new GuzzleAdapter();
    $gateway = new ApiGatewayHttp($client);

    $handler = new CreateProductsHandler($gateway);
    $handler->setNext(new CreatePricesHandler());

    $createProductsUsecase = new CreateProductsUsecase($gateway, $handler);
    $createPricesUsecase = new CreatePricesUsecase($gateway, $handler);
    $createPromotionalPricesUsecase = new CreatePromotionalPricesUsecase($gateway, $handler);

    $cronHandler = new CronHandlerImpl();
    new CronController(
        $cronHandler,
        $createProductsUsecase,
        $createPricesUsecase,
        $createPromotionalPricesUsecase
    );
    $command = $argv[1] || '';

    $cronHandler->type($command);
} catch (\Throwable $th) {
    echo $th;
}
