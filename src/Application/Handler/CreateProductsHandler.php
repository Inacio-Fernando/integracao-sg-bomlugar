<?php

namespace IntegracaoSgsistemas\Application\Handler;

use IntegracaoSgsistemas\Application\Gateways\ApiGateway;
use IntegracaoSgsistemas\Infra\Database\Models\ProductTable;
use IntegracaoSgsistemas\Infra\Log\Log;
use Throwable;

class CreateProductsHandler extends AbstractHandler
{
    private $gateway;
    public function __construct(ApiGateway $gateway)
    {
        $this->gateway = $gateway;
    }
    public function handle(array $data): array
    {
        $filial = $data['filial'];
        if (isset($data['products'])) {
            $productsData = $data['products'];
            $insertionData = array();
            $updateData = array();
            $productCodes  = array_column($productsData, 'id');

            $products = ProductTable::whereIn('prod_cod', $productCodes)->get(['prod_id', 'prod_cod', 'prod_sku']);
            $products = $products->toArray();
            $productIds = array_column($products, 'prod_id', 'prod_cod');
            $productSkus = array_column($products, 'prod_sku', 'prod_cod');


            foreach ($productsData as $productData) {
                try {
                    Log::info('Create Products: Criando o produto: ' . json_encode($productData));
                    $code = $productData['id'];
                    $productId = $productIds[$code] ?? null; //$products->firstWhere('prod_cod', $code); 
                    $gtins = $this->gateway->getProductGtin($code);
                    $gtins = array_column($gtins, 'idGTIN');
                    $gtins = array_map('trim', $gtins);
                    $secao = $this->gateway->getProductSecao($productData['departamentalizacaoNivel1']);
                    $grupo = $this->gateway->getProductGrupo($secao['departamentalizacaoNivel2']);
                    $subgrupo = $this->gateway->getProductSubgrupo($grupo['departamentalizacaoNivel3']);


                    if (!$productId) {
                        $insertionData[] = [
                            'prod_cod'              => $code,
                            'prod_nome'             => $productData['descricao'],
                            'prod_desc'             => $productData['descricao'],
                            'prod_proporcao'        => $productData['unidadeDeMedida'],
                            'prod_sessao'           => $secao['descricao'],
                            'prod_grupo'            => $grupo['descricao'],
                            'prod_subgrupo'         => $subgrupo['descricao'],
                            'prod_empresa'          => 1,
                            'prod_estabelecimento'  => 1,
                            'prod_sku'              => implode(',', array_unique($gtins)),
                            'prod_flag100g'         => '',
                            'prod_embalagem'        =>  null
                        ];
                        continue;
                    }
                    $updateData[] = [
                        'prod_id' => $productId,
                        'prod_sku' => implode(',', array_unique(array_merge(explode(",", $productSkus[$code]), $gtins))),
                        'prod_nome' => $productData['descricao'],
                        'prod_desc' => $productData['descricao'],
                        'prod_proporcao' => $productData['unidadeDeMedida'],
                        'prod_sessao'   => $secao['descricao'],
                        'prod_grupo'    => $grupo['descricao'],
                        'prod_subgrupo' => $subgrupo['descricao']
                    ];
                } catch (Throwable $e) {
                    Log::error('Create Products: Falha ao salvar produto. ' . json_encode($productData));
                }
            }
            $result = ProductTable::batchInsert(
                [
                    'prod_cod',
                    'prod_nome',
                    'prod_desc',
                    'prod_proporcao',
                    'prod_sessao',
                    'prod_grupo',
                    'prod_subgrupo',
                    'prod_empresa',
                    'prod_estabelecimento',
                    'prod_sku',
                    'prod_flag100g',
                    'prod_embalagem'
                ],
                $insertionData,
                500
            );
            if (count($insertionData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Products: Falha ao inserir produtos \n\tProdutos: " . json_encode($insertionData));
            }

            $result = ProductTable::batchUpdate($updateData, 'prod_id');
            if (count($updateData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Products: Falha ao atualizar produtos \n\tProdutos: " . json_encode($updateData));
            }
        }
        if (isset($data['prices'])) {
            $insertData = array();
            $updateData = array();
            $productsData = $data['prices'];
            $productCodes = array_unique(array_column($productsData, 'idProduto'));

            $products = ProductTable::whereIn('prod_cod', $productCodes)->get(['prod_id', 'prod_cod']);
            $products = $products->toArray();

            $missingProdutos = array_diff($productCodes, array_column($products, 'prod_cod'));


            foreach ($missingProdutos as $code) {
                try {
                    $productData = $this->gateway->getProduct($filial, $code);
                    Log::info('Create Products: Criando o produto: ' . json_encode($productData));

                    $gtins = $this->gateway->getProductGtin($code);
                    $gtins = array_column($gtins, 'idGTIN');
                    $gtins = array_map('trim', $gtins);
                    $secao = $this->gateway->getProductSecao($productData['departamentalizacaoNivel1']);
                    $grupo = $this->gateway->getProductGrupo($secao['departamentalizacaoNivel2']);
                    $subgrupo = $this->gateway->getProductSubgrupo($grupo['departamentalizacaoNivel3']);

                    $insertData[] = [
                        'prod_cod'              => $code,
                        'prod_nome'             => $productData['descricao'],
                        'prod_desc'             => $productData['descricao'],
                        'prod_proporcao'        => $productData['unidadeDeMedida'],
                        'prod_sessao'           => $secao['descricao'],
                        'prod_grupo'            => $grupo['descricao'],
                        'prod_subgrupo'         => $subgrupo['descricao'],
                        'prod_empresa'          => 1,
                        'prod_estabelecimento'  => 1,
                        'prod_sku'              => implode(',', array_unique($gtins)),
                        'prod_flag100g'         => '',
                        'prod_embalagem'        =>  null
                    ];
                } catch (Throwable $e) {
                    Log::error('Create Missing Products: Falha ao criar produto. ' . json_encode($productData));
                }
            }
            $result = ProductTable::batchInsert(
                [
                    'prod_cod',
                    'prod_nome',
                    'prod_desc',
                    'prod_proporcao',
                    'prod_sessao',
                    'prod_grupo',
                    'prod_subgrupo',
                    'prod_empresa',
                    'prod_estabelecimento',
                    'prod_sku',
                    'prod_flag100g',
                    'prod_embalagem'
                ],
                $insertData,
                500
            );
            if (count($insertData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Missing Products: Falha ao inserir produtos \n\tProdutos: " . json_encode($insertData));
            }
        }
        if (isset($data['promotions'])) {
            $insertData = array();
            $updateData = array();

            $productsData = $data['promotions'];
            $productCodes  = array_unique(array_column(array_column($productsData, 'produtos'), 'idProduto'));

            $products = ProductTable::whereIn('prod_cod', $productCodes)->get(['prod_id', 'prod_cod']);
            $products = $products->toArray();

            $missingProdutos = array_diff($productCodes, array_column($products, 'prod_cod'));


            foreach ($missingProdutos as $code) {
                try {
                    $productData = $this->gateway->getProduct($filial, $code);
                    Log::info('Create Products: Criando o produto: ' . json_encode($productData));

                    $gtins = $this->gateway->getProductGtin($code);
                    $gtins = array_column($gtins, 'idGTIN');
                    $gtins = array_map('trim', $gtins);
                    $secao = $this->gateway->getProductSecao($productData['departamentalizacaoNivel1']);
                    $grupo = $this->gateway->getProductGrupo($secao['departamentalizacaoNivel2']);
                    $subgrupo = $this->gateway->getProductSubgrupo($grupo['departamentalizacaoNivel3']);

                    $insertionData[] = [
                        'prod_cod'              => $code,
                        'prod_nome'             => $productData['descricao'],
                        'prod_desc'             => $productData['descricao'],
                        'prod_proporcao'        => $productData['unidadeDeMedida'],
                        'prod_sessao'           => $secao['descricao'],
                        'prod_grupo'            => $grupo['descricao'],
                        'prod_subgrupo'         => $subgrupo['descricao'],
                        'prod_empresa'          => 1,
                        'prod_estabelecimento'  => 1,
                        'prod_sku'              => implode(',', array_unique($gtins)),
                        'prod_flag100g'         => '',
                        'prod_embalagem'        =>  null
                    ];
                } catch (Throwable $e) {
                    Log::error('Create Missing Promotional Products: Falha ao criar produto. ' . json_encode($productData));
                }
            }
            $result = ProductTable::batchInsert(
                [
                    'prod_cod',
                    'prod_nome',
                    'prod_desc',
                    'prod_proporcao',
                    'prod_sessao',
                    'prod_grupo',
                    'prod_subgrupo',
                    'prod_empresa',
                    'prod_estabelecimento',
                    'prod_sku',
                    'prod_flag100g',
                    'prod_embalagem'
                ],
                $insertData,
                500
            );
            if (count($insertData) > 0 && $result === false) {
                Log::channel('database-error')->error("Create Missing Promotional Products: Falha ao inserir produtos \n\tProdutos: " . json_encode($insertData));
            }
        }
        return parent::handle($data);
    }
}
