<?php


namespace IntegracaoSgsistemas\Infra\Gateways;

use GuzzleHttp\Promise\Utils;
use IntegracaoSgsistemas\Application\Gateways\ApiGateway;
use IntegracaoSgsistemas\Infra\Http\HttpClient;


class ApiGatewayHttp implements ApiGateway {

    private $httpClient;
	private $key;
	private $headers;

	public function __construct (HttpClient $httpClient, $token = null ) {
		$this->httpClient = $httpClient;
		$this->key = $token ?? $this->generateKey();
		$this->headers = [
			'Authorization' => $this->key
		  ];
	}

	
	private function generateKey() {
		$body = [
			'usuario' => "cartazfacil",
			'senha' => 'js&5wc8i$@L$bX6&BDOs'
		];
		$this->headers = [
			'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36",
		];
		$response = $this->httpClient->post("autorizacao", $body, $this->headers);
		return $response['token']; 
	}

	public function getAllFiliais($batchSize = 1000) {

		$filiais = $this->httpClient->get("filiais",$this->headers);
		$filiais = $filiais['dados'];
		return $filiais;
	}

	// public function getProductsByFilial($filialId, $batchSize = 1000) {
		
	// 	$dataAtual = date('d/m/Y');
	// 	$currentPage = 0;

	// 	while (true) {
	// 		$url = "produtos?filial=$filialId&filtroDataTipo=dataAlteracaoPreco&filtroDataInicial=$dataAtual&pagina=$currentPage&itensPorPagina=$batchSize";
	// 		$response = $this->httpClient->getAsync($url,$this->headers);
	// 		$products = $response['data'];
	// 		if (empty($products)) {
	// 			break; // Sem mais dados, saímos do loop
	// 		}
	// 		yield $products;

	// 		$currentPage++;
	// 	}
	// }	

	public function getProductsByFilial($filialId, $batchSize = 100) {
		$dataAtual = date('Y-m-d');
		$currentPage = 1;
		$totalPages = null;
	
		while (true) {
			$promises = [];
	
			for ($i = 1; $i <=  5; $i++) { // Buscar 10 páginas em paralelo
				
				$url = "produtos?filial=$filialId&pagina=$currentPage&itensPorPagina=$batchSize&filtroDataTipo=dataAlteracaoPreco&filtroDataInicial=$dataAtual&ativo=true";
																								
				$promises[] = $this->httpClient->getAsync($url, $this->headers);
				$currentPage++;
			}
	
			// Esperar todas as promessas serem resolvidas
			$responses = Utils::settle($promises)->wait();
	
			$allProducts = [];
			foreach ($responses as $response) {
				if ($response['state'] === 'fulfilled') {
					$products = $response['value']['dados'];
					$allProducts = array_merge($allProducts, $products);
	
					// Extrair detalhes de paginação se não estiver definido
					if ($totalPages === null) {
						$pagination = $response['value']['paginacao'];
						$totalPages = $pagination['quantidadePaginas'];
					}
	
					// Sair do loop se não forem encontrados produtos
					if (empty($products)) {
						break; // Sair de ambos os loops
					}
				} else {
					// Lidar com casos de erro se necessário
				}
			}
	
			if (!empty($allProducts)) {
				yield $allProducts;
			}
	
			// Sair do loop se todas as páginas forem buscadas
			if ($currentPage >= $totalPages) {
				break;
			}
		}
	}

	public function getProduct($filialId, $productKey) {
		$url = "produtos?filial={$filialId}&id={$productKey}";
		$response = $this->httpClient->getAsync($url,$this->headers);
		return $response;
		
	}

	public function getPricesByFilial($filialId, $batchSize = 1000) {
		
		$url = "produtos/precos?filial=$filialId";
		$response = $this->httpClient->getAsync($url,$this->headers);
		$prices = $response['dados'];
		return $prices;
		
	}
	public function getPrice($productKey, $filialId) {
		$url = "vendas/precos?produtoKey=$productKey&lojaKey=$filialId";
		$response = $this->httpClient->getAsync($url,$this->headers);
		$price = $response["data"];
		return $price[0];
		
	}

	public function getPromotionsByFilial($filialId, $batchSize=100) {
		$url ="ofertas?filial={$filialId}";
		$response = $this->httpClient->getAsync($url,$this->headers);
		return $response['dados'];
	}

	public function getProductSecao($id) {
		$secao = $this->httpClient->get("departamentos/nivel1?id=$id",$this->headers);
		return $secao;
	}
	public function getProductGrupo($id) {
		$grupo = $this->httpClient->get("departamentos/nivel2?id=$id",$this->headers);
		return $grupo;
	}
	public function getProductSubgrupo($id) {
		$subgrupo = $this->httpClient->get("departamentos/nivel3?id=$id",$this->headers);
		return $subgrupo;
	}

	public function getProductGtin($productCode) {
		$gtins = $this->httpClient->get("produtos/gtins?idProduto=$productCode",$this->headers);
		$gtins = $gtins['GTINs'];
		return $gtins;
	}

}