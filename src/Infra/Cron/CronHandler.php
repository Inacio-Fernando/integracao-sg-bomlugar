<?php

namespace IntegracaoSgsistemas\Infra\Cron;

use stdClass;

abstract class CronHandler {
    public $commands;

	public function __construct() {
		$this->commands = new stdClass();
	}

    function on (string $command, callable $callback) {
		$this->commands->$command = $callback;
	}

	function type (string $text) {
		$command = explode(" ", $text)[0];
		if (!$this->commands->$command) return;
		$params = trim(str_replace($command, "", $text));
		($this->commands->$command)($params);
	}

	abstract function write (string $text): void;

} 