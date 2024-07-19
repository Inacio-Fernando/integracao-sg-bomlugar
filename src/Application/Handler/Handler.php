<?php

namespace IntegracaoSgsistemas\Application\Handler;

interface Handler {
    public function setNext(Handler $handler): Handler;
    public function handle(array $data): array;
}