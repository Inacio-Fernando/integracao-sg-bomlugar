<?php
namespace IntegracaoSgsistemas\Infra\Database;
use IntegracaoSgsistemas\Application\Repositories\RulesRepository;

class RulesRepositoryAdapter implements RulesRepository {
    private $connection;
    function __construct($connection) {
        $this->connection = $connection;
      }

      public function getAppPrices() {
        $dataAtual = date('Y-m-d'); 
        $result = $this->connection->query("
        SELECT * FROM (
        select 
        SEQPRODUTO as CODIGO,
        SEQEMPRESA as NROEMPRESA,
        PRECOLOJA,
        PRECOPROMO,
        DATAINICIO as DTAINICIO,
        DATAFIM as DTAFIM,
        QTDE,
        IDPROMOIDEVER,
        SEQFAMILIA,
        FAMILIA,
        ROW_NUMBER() OVER (PARTITION BY SEQFAMILIA, SEQEMPRESA ORDER BY SEQPRODUTO) AS rn
        from CTZFCL_IDEVER 
        WHERE QTDE > 0 
        AND TO_CHAR(DATAFIM, 'YYYY-MM-DD') >= '$dataAtual'
        ) CTZFCL_IDEVER_WITH_RN 
        WHERE rn = 1
        ");
        return $result;
    }

      public function getPrices() {
        $dataAtual = date('Y-m-d'); 
        $result = $this->connection->query("
        select 
        NROEMPRESA,
        CODIGO, 
        MULTEQPEMBALAGEM,
        PRECOREGULAR, 
        PRECOPROMOCIONAL, 
        PRECOREGULARFATOR 
        from CTZFCL_PRECOS 
        WHERE TO_CHAR(DTAVALIDACAOPRECO, 'YYYY-MM-DD')='$dataAtual'
        ");
        return $result;
    }

    public function getPromotions() {
        
        $dataAtual = date('Y-m-d'); 
        $result = $this->connection->query(
        "
        SELECT *
        FROM (
        SELECT 
        SEQFAMILIA,
        NROEMPRESA,
        SEQPRODUTO as CODIGO,
        ROW_NUMBER() OVER (PARTITION BY cp.SEQFAMILIA, cp.NROEMPRESA ORDER BY cp.SEQPRODUTO DESC) AS rn,
        DESC_ENC_FAMILIA as FAMILIA,
        DTAFIM,
        DTAINICIO,
        PRECO_BASE,
        PRECO_MASCOTE,
        PRECO_PROMOCIONAL,
        PROMOCAO
        FROM CONSINCO.CTZFCL_PROMOCAO cp
        WHERE TO_CHAR(DTAFIM, 'YYYY-MM-DD') >= '$dataAtual') cp_with_rownum
        WHERE rn = 1
        ");
        return $result;
    }

    public function getProducts() {
        $dataAtual = date('Y-m-d'); 
        
        $result = $this->connection->query("
        select 
        NOME, 
        CODIGO, 
        CAST(substr(EANINTERNO,1,60) as varchar(255)) AS EANINTERNO, 
        SECAO, 
        GRUPO,
        SUBGRUPO, 
        PROPORCAO 
        from CTZFCL_PRODUTOS 
        WHERE TO_CHAR(DTAHORALTERACAO, 'YYYY-MM-DD')='$dataAtual'
        ");
        return $result;
    }
}