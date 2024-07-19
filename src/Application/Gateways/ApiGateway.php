<?php

namespace IntegracaoSgsistemas\Application\Gateways;

interface ApiGateway {
	public function getProductsByFilial($filialId, $batchSize = 100);
	public function getPricesByFilial($filialId, $batchSize = 1000);
	public function getPromotionsByFilial($filialId, $batchSize = 100);
	public function getAllFiliais($batchSize = 1000);
	public function getProduct($filialId, $productKey);
	public function getPrice($productKey, $filialId);
	public function getProductGtin($productCode);
	public function getProductSecao($productCode);
	public function getProductGrupo($productCode);
	public function getProductSubgrupo($productCode);
}