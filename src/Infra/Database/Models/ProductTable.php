<?php

namespace IntegracaoSgsistemas\Infra\Database\Models;

use Illuminate\Database\Eloquent\Model;
use IntegracaoSgsistemas\Infra\Database\Database;
use Throwable;

/**
 * @property integer $prod_id
 * @property string $prod_nome
 * @property string $prod_apresentacao
 * @property string $prod_embalagem
 * @property string $prod_sessao
 * @property string $prod_grupo
 * @property string $prod_subgrupo
 * @property string $prod_descricao
 * @property integer $prod_empresa
 * @property integer $prod_estabelecimento
 * @property string $prod_cod
 * @property integer $prod_filial
 * @property string $prod_sku
 * @property string $prod_proporcao
 * @property string $prod_desc
 * @property string $prod_revisao
 * @property string $prod_flag100g
 * @property string $prod_desc_alt
 */
class ProductTable extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cf_produto';
	public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'prod_id';

    /**
     * @var array
     */
    protected $fillable = [
        'prod_nome',
        'prod_apresentacao',
        'prod_embalagem',
        'prod_sessao',
        'prod_grupo',
        'prod_subgrupo',
        'prod_descricao',
        'prod_empresa',
        'prod_estabelecimento',
        'prod_cod',
        'prod_filial',
        'prod_sku',
        'prod_proporcao',
        'prod_desc',
        'prod_revisao',
        'prod_flag100g',
        'prod_desc_alt'
    ];

    public static function batchInsert( $columns, $data, $batchSize)
	{
        try {
            $batch = Database::getBatch();
            $result = $batch->insert(new ProductTable, $columns, $data, $batchSize);
            return $result;
        } catch(Throwable $e) {
            return false;
        }
	}

	public static function batchUpdate( $data, $batchSize)
	{
        try {
            $batch = Database::getBatch();
            $result = $batch->update(new ProductTable, $data, $batchSize);
            return $result;
        } catch(Throwable $e) {
            return false;
        }
	}



    public function prices() {
        return $this->hasMany(TabelaValor::class, 'vlr_produto');
    }
}



