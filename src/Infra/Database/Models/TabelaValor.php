<?php

namespace IntegracaoSgsistemas\Infra\Database\Models;

use Illuminate\Database\Eloquent\Model;
use IntegracaoSgsistemas\Infra\Database\Database;
use Throwable;

/**
 * @property integer $vlr_id
 * @property integer $vlr_produto
 * @property string $vlr_data_de
 * @property string $vlr_data_ate
 * @property integer $vlr_idcomercial
 * @property string $vlr_empresa
 * @property string $vlr_filial
 * @property string $vlr_usuario
 * @property string $vlr_valores
 * @property string $vlr_hora
 */
class TabelaValor extends Model
{

	/**
	 * The table associated with the model.
	 * 
	 * @var string
	 */
	protected $table = 'cf_valor';
	public $timestamps = false;

	/**
	 * The primary key for the model.
	 * 
	 * @var string
	 */
	protected $primaryKey = 'vlr_id';

	/**
	 * @var array
	 */
	protected $fillable = [
		'vlr_produto', 'vlr_data_de', 'vlr_data_ate', 'vlr_idcomercial',
		'vlr_empresa', 'vlr_filial', 'vlr_usuario', 'vlr_valores', 'vlr_hora'
	];

	public static function batchInsert( $columns, $data, $batchSize)
{		try {
	$batch = Database::getBatch();
	$result = $batch->insert(new TabelaValor, $columns, $data, $batchSize);
	return $result;
} catch(Throwable $e) {
	return false;
}
	}

	public static function batchUpdate( $data, $batchSize)
	{
		try {
			$batch = Database::getBatch();
			$result = $batch->update(new TabelaValor, $data, $batchSize);
			return $result;
		} catch(Throwable $e) {
			return false;
		}
	}

	public function product() {
		return $this->belongsTo(ProductTable::class, 'vlr_produto');
	}

}
