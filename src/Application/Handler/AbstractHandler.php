<?php

namespace IntegracaoSgsistemas\Application\Handler;

abstract class AbstractHandler implements Handler
{
    private $nextHandler;

    public function setNext(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(array $data): array
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($data);
        }

        return $data;
    }
}
