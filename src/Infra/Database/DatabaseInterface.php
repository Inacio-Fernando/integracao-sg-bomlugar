<?php

namespace IntegracaoSgsistemas\Infra\Database;

interface DatabaseInterface
{
    public function close();
    public function getConnection();
    public function query($query);

}
