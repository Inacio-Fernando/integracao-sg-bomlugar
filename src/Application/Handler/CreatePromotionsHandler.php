<?php

namespace IntegracaoSgsistemas\Application\Handler;

use IntegracaoSgsistemas\Infra\Database\Models\ProductTable;

class CreatePromotionsHandler extends AbstractHandler
{
    public function handle(array $data): array
    {
        $filial = $data['filial'];
        if(isset($data['promotions'])) {
            $insertData = array();
            $updateData = array();
            $promotionsData = $data['promotions'];

            $productCodes  = array_column(array_column($promotionsData, 'produtos'), 'idProduto');

            $products = ProductTable::with('prices')->whereIn('prod_cod', array_unique($productCodes))->get(['prod_id', 'prod_cod', 'prod_filial', 'prod_desc', 'prod_sessao', 'prod_grupo']);
            $products = $products->keyBy('prod_cod');


            foreach($promotionsData as $promotionData) {
                foreach($promotionData['produtos'] as $productData) {
                    $code = $productData['idProduto'];
                    $products->get($code);

                    $startDate = date('Y-m-d', strtotime($productData['dataInicial']));

                }
            }

        }
        return parent::handle($data);

    }
}