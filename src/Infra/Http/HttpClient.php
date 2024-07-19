<?php

namespace IntegracaoSgsistemas\Infra\Http;

interface HttpClient {
	public function get ($url, $headers = null);
	public function post ($url, $body, $headers = null);
	public function getAsync ($urls, $headers = null);
	public function fetchPaginatedData ($url, $headers = null, $itemsPerPage = 100, $concurrency = 5);
}