<?php

namespace IntegracaoSgsistemas\Infra\Cron;

use IntegracaoSgsistemas\Application\Usecases\Usecase;

class CronController {

    public function __construct(
        CronHandler $handler, 
        Usecase $createProductsUsecase,
        Usecase $createPricesUsecase,
        Usecase $createPromotionalPricesUsecase
        ) {
            $handler->on("create-all", function($params = null) use (
                $createProductsUsecase, $createPricesUsecase, $createPromotionalPricesUsecase
                ) {

                $start = microtime(true);

                $createProductsUsecase->execute();
                $createPricesUsecase->execute();
                // $createPromotionalPricesUsecase->execute();
                echo "\nScript Finalizado -- Tempo de execução: " . (microtime(true) - $start) . "\n";

            });
    }

}