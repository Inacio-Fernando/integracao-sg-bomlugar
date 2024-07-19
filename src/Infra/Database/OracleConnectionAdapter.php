<?php

namespace IntegracaoSgsistemas\Infra\Database;

use IntegracaoSgsistemas\Infra\Log\Log;
use PDO;
use PDOException;
use Throwable;
class OracleConnectionAdapter implements DatabaseInterface
{
    private $connection;

    public function __construct() {
        $mydb = "(DESCRIPTION =
            (ADDRESS_LIST =
            (ADDRESS = (PROTOCOL = TCP)(HOST = 168.138.147.243)(PORT = 1521))
            )
            (CONNECT_DATA =
            (SERVICE_NAME = CONSINCO)
            )
            )
            ";

		$conn_username = "CARTAZFACIL";
		$conn_password = "KgcdArM27xXfUWq";

		$opt = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM
		];
        try {
            $connection = new PDO("oci:dbname=" . $mydb, $conn_username, $conn_password, $opt);
        } catch (Throwable $e) {
            Log::error("Não foi possível conectar ao banco de dados \n\n" . $e->getMessage());
            throw $e;
        }
        
        $this->connection = $connection;
    }
    public function close() {
        $this->connection = null;
    }
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($query) {
        try {
            $stmt = $this->connection->prepare($query);
			$stmt->execute();
			$result =$stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($result) == 0) {
                Log::channel('dumps')->error('Resultado da query vazio');
            }
            return $result;
        } catch (PDOException $e) {
            Log::error("Não foi possível executar a query \n\n {$e->getMessage()}");
            return null;
        }
    }
}
