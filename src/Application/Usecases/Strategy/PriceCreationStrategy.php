<?php

namespace IntegracaoSgsistemas\Application\Usecases\Strategy;

interface PriceCreationStrategy {
    public function getValueAndDynamic($product): array;
    public function getPricesData() : array;
}