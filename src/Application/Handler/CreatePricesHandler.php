<?php

namespace IntegracaoSgsistemas\Application\Handler;

use IntegracaoSgsistemas\Domain\DomainServices\ValueFormatter;
use IntegracaoSgsistemas\Infra\Database\Models\ProductTable;
use IntegracaoSgsistemas\Infra\Database\Models\TabelaValor;
use IntegracaoSgsistemas\Infra\Log\Log;

class CreatePricesHandler extends AbstractHandler
{
    public function handle(array $data): array
    {
        $filial = $data['filial'];
        if (isset($data['products'])) {
            $insertData = array();
            $updateData = array();

            $productsData = $data['products'];
            $productCodes = array_column($productsData, 'id');
            $products = ProductTable::with('prices')->whereIn('prod_cod', array_unique($productCodes))->get(['prod_id', 'prod_cod']);
            $products = $products->keyBy('prod_cod');
            foreach ($productsData as $productData) {
                $code = $productData['id'];
                $product = $products->get($code);
                if (!$product) {
                    Log::error("Create Base Prices: Falha ao buscar produto: " . json_encode($productData));
                    continue;
                }
                Log::info("Create Base Prices: Criando preço: " . json_encode($productData));

                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d');
                $vlr_id_comercial = 1;
                $value = ValueFormatter::formatNumber($productData['precoDeVenda1']);

                $price = $product->prices->first(function ($price) use ($filial, $vlr_id_comercial) {
                    return ($price->vlr_filial == $filial) && ($price->vlr_idcomercial == $vlr_id_comercial);
                });

                if (!$price) {
                    $insertData[] = [
                        'vlr_produto' => $product->prod_id,
                        'vlr_idcomercial' => $vlr_id_comercial,
                        'vlr_filial' => $filial,
                        'vlr_data_de' => $startDate,
                        'vlr_data_ate' => $endDate,
                        'vlr_valores' => $value,
                        'vlr_hora' => date('H:i'),
                        'vlr_empresa' => 1,
                        'vlr_usuario' => 1
                    ];
                    continue;
                }

                $updateData[] = [
                    'vlr_id' => $price->vlr_id,
                    'vlr_data_de' => $startDate,
                    'vlr_data_ate' => $endDate,
                    'vlr_valores' => $value,
                    'vlr_hora' => date('H:i')
                ];
            }
            $columns = [
                'vlr_produto',
                'vlr_idcomercial',
                'vlr_filial',
                'vlr_data_de',
                'vlr_data_ate',
                'vlr_valores',
                'vlr_hora',
                'vlr_empresa',
                'vlr_usuario'
            ];
            $result = TabelaValor::batchInsert($columns, $insertData, 500);
            if (count($insertData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Base Prices: Falha ao inserir preços \n\tPreços: " . $insertData);
            }

            $result = TabelaValor::batchUpdate($updateData, 'vlr_id');
            if (count($updateData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Base Prices: Falha ao atualizar preços\n\tPreços: " . $updateData);
            }
        }
        if (isset($data['prices'])) {
            $insertData = array();
            $updateData = array();

            $pricesData = $data['prices'];
            $productCodes = array_column($pricesData, 'idProduto');
            $products = ProductTable::with('prices')->whereIn('prod_cod', array_unique($productCodes))->get(['prod_id', 'prod_cod']);
            $products = $products->keyBy('prod_cod');
            foreach ($pricesData as $priceData) {
                $code = $priceData['idProduto'];
                $product = $products->get($code);
                if (!$product) {
                    Log::error("Create Prices: Falha ao buscar produto: " . json_encode($priceData));
                    continue;
                }
                Log::info("Create Prices: Criando preço: " . json_encode($priceData));

                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d');
                $basePrice = $product->prices->first(function ($price) use ($filial) {
                    return ($price->vlr_filial == $filial) && ($price->vlr_idcomercial == 1);
                });
                // $promotionalPrice = $priceData['precos'][0] ?? null;
                $promotionalPrice = array_filter($priceData['precos'], function($price) {
                    return $price['quantidadeAtacado'] == 0;
                })[0] ?? null;

                if (!$promotionalPrice || $promotionalPrice['precoVenda'] <= 0) {
                    Log::error("Create Prices: Nenhum preço para ser criado " . json_encode($priceData));
                    continue;
                }
                $promotionalPrice = ValueFormatter::formatNumber($promotionalPrice['precoVenda']);


                if ($basePrice) {
                    $updateData[] = [
                        'vlr_id' => $basePrice->vlr_id,
                        'vlr_data_de' => $startDate,
                        'vlr_data_ate' => $endDate,
                        'vlr_valores' => $promotionalPrice,
                        'vlr_hora' => date('H:i')
                    ];
                    continue;
                }

                $insertData[] = [
                    'vlr_produto' => $product->prod_id,
                    'vlr_idcomercial' => 1,
                    'vlr_filial' => $filial,
                    'vlr_data_de' => $startDate,
                    'vlr_data_ate' => $endDate,
                    'vlr_valores' => $promotionalPrice,
                    'vlr_hora' => date('H:i'),
                    'vlr_empresa' => 1,
                    'vlr_usuario' => 1
                ];
            }
            $columns = [
                'vlr_produto',
                'vlr_idcomercial',
                'vlr_filial',
                'vlr_data_de',
                'vlr_data_ate',
                'vlr_valores',
                'vlr_hora',
                'vlr_empresa',
                'vlr_usuario'
            ];
            $result = TabelaValor::batchInsert($columns, $insertData, 500);
            if (count($insertData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Prices: Falha ao inserir preços \n\tPreços: " . $insertData);
            }

            $result = TabelaValor::batchUpdate($updateData, 'vlr_id');
            if (count($updateData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Prices: Falha ao atualizar preços\n\tPreços: " . $updateData);
            }
        }
        if (isset($data['promotions'])) {
            $insertData = array();
            $updateData = array();

            $promotionsData = $data['promotions'];
            $productCodes  = array_column(array_column($promotionsData, 'produtos'), 'idProduto');
            $products = ProductTable::with('prices')->whereIn('prod_cod', array_unique($productCodes))->get(['prod_id', 'prod_cod']);
            $products = $products->keyBy('prod_cod');


            foreach ($promotionsData as $promotionData) {
                foreach ($promotionData['produtos'] as $productData) {

                    $code = $priceData['idProduto'];
                    $product = $products->get($code);
                    if (!$product) {
                        Log::error("Create Promotional Prices: Falha ao buscar produto: " . json_encode($productData));
                        continue;
                    }
                    Log::info("Create Promotional Prices: Criando preço: " . json_encode($productData));

                    $startDate = date('Y-m-d', strtotime($productData['dataInicial']));
                    $endDate = date('Y-m-d', strtotime($productData['dataFinal']));

                    $basePrice = $product->prices->first(function ($price) use ($filial) {
                        return ($price->vlr_filial == $filial) && ($price->vlr_idcomercial == 1);
                    });
                    $promotionalPrice = ValueFormatter::formatNumber($priceData['preco']);
                    $vlr_id_comercial = 1;
                    $value = $basePrice->vlr_valores;
                    if($productData['descontoClienteFidelizado'] > 0) {
                        $vlr_id_comercial = 3;
                        $value .= $promotionalPrice;
                    } else {
                        $value = $promotionalPrice;
                    }
                    
                    $price = $product->prices->first(function ($price) use ($filial, $vlr_id_comercial) {
                        return ($price->vlr_filial == $filial) && ($price->vlr_idcomercial == $vlr_id_comercial);
                    });

                    if ($price) {
                        $updateData[] = [
                            'vlr_id' => $basePrice->vlr_id,
                            'vlr_data_de' => $startDate,
                            'vlr_data_ate' => $endDate,
                            'vlr_valores' => $value,
                            'vlr_hora' => date('H:i')
                        ];
                        continue;
                    }

                    $insertData[] = [
                        'vlr_produto' => $product->prod_id,
                        'vlr_idcomercial' => $vlr_id_comercial,
                        'vlr_filial' => $filial,
                        'vlr_data_de' => $startDate,
                        'vlr_data_ate' => $endDate,
                        'vlr_valores' => $value,
                        'vlr_hora' => date('H:i'),
                        'vlr_empresa' => 1,
                        'vlr_usuario' => 1
                    ];
                }
            }
            $columns = [
                'vlr_produto',
                'vlr_idcomercial',
                'vlr_filial',
                'vlr_data_de',
                'vlr_data_ate',
                'vlr_valores',
                'vlr_hora',
                'vlr_empresa',
                'vlr_usuario'
            ];
            $result = TabelaValor::batchInsert($columns, $insertData, 500);
            if (count($insertData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Promotional Prices: Falha ao inserir preços \n\tPreços: " . $insertData);
            }

            $result = TabelaValor::batchUpdate($updateData, 'vlr_id');
            if (count($updateData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Promotional Prices: Falha ao atualizar preços\n\tPreços: " . $updateData);
            }
        }
        return parent::handle($data);
    }
}
