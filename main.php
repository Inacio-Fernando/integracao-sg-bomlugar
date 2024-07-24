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
$gateway = new ApiGatewayHttp($client, "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvIjoiY2FydGF6ZmFjaWwiLCJleHBpcmVfdGltZSI6IjIwMjQtMDctMjQgMTY6NDE6NDYifQ.MCVhHM2v0A0nvDc14BCy5qk6g60AG450NuQP8ViDSjU");

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
$command = $_GET['script'];
$cronHandler->type('create-all');

} catch (\Throwable $th) {
    echo $th;
}