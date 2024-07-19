<?php

namespace IntegracaoSgsistemas\Infra\Http;

use Error;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise;
use IntegracaoSgsistemas\Infra\Log\Log;

class GuzzleAdapter implements HttpClient {
    private $client;

	public function __construct () {
        $this->client = new Client(
		 [
			'base_uri' => "http://177.72.160.190:8069/integracao/sgsistemas/v1/",
			// 'base_uri' => "http://sgps.sgsistemas.com.br:8201/integracao/sgsistemas/v1/",
			'Accept' => 'application/json',
	        'Content-Type' => 'application/json'
		]);
    }

	public function get($url, $headers = null) {
		$response = $this->client->request('GET', $url, [
			'headers' => $headers
		]);
		if ($response->getStatusCode() !== 200) {
			Log::error($response->getReasonPhrase());
			return [];
		}
		return json_decode($response->getBody(), true);
	}

	public function post($url, $body, $headers = null) {
		
		$response = $this->client->request('POST', $url, [
			'headers' => $headers,
			'json' => $body
		]);
		if ($response->getStatusCode() !== 200) {
			Log::error($response->getReasonPhrase());
			return [];
		}
		return json_decode($response->getBody(), true);
	}

	public function getAsync ($url, $headers = null) {
		
		$response = $this->client->getAsync($url, ['headers' => $headers]);
		$response = $response->wait();
		if ($response->getStatusCode() !== 200) {
			Log::error($response->getReasonPhrase());
			return [];
		}
		return json_decode($response->getBody(), true); 

	}


	function fetchPaginatedData($url, $headers = null, $itemsPerPage = 100, $concurrency = 20) {
		$allData = [];
		$currentPage = 1;
		$totalPages = 1;

		try {
			$firstUrl = $url['pageUrl'] . $currentPage . $url['itemsPerPageUrl'] . $itemsPerPage;
			$response = $this->client->request('GET', $firstUrl, [
				'headers' => $headers
			]);
			$responseData = json_decode($response->getBody(), true);

			if (isset($responseData['paginacao'])) {
				$totalPages = $responseData['paginacao']['quantidadePaginas'];
			}

			if (isset($responseData['dados'])) {
				$allData = array_merge($allData, $responseData['dados']);
			}
			} catch (RequestException $e) {
				throw new Exception("Erro ao fazer requisição inicial: " . $e->getMessage());
			}
			$promises =[];
			
			for ($page = 2; $page <= $totalPages; $page++) {
				$requestUrl = "http://sgps.sgsistemas.com.br:8201/integracao/sgsistemas/v1/" . $url['pageUrl'] .  $page . $url['itemsPerPageUrl'] . $itemsPerPage;
				$promises[$page] =  $this->client->getAsync($requestUrl, ['headers' => $headers]);
			}
			$responses = Promise\Utils::settle($promises)->wait();
			
			foreach($responses as $index => $response) {
				if($response['state'] != 'fulfilled') {
					Log::error('Não foi possível fazer a requisição para a página'. $index);
					continue;
				}			
				$data = json_decode($response['value']->getBody(),true);
				$allData = array_merge($allData, $data['dados']);
			}

		return $allData;
	}
}