<?php

namespace IntegracaoSgsistemas\Domain\ValueObjects;

use ReflectionClass;

class ProductCategory
{

    const DEFAULT = array(

        'ACOUGUE' => array('tamanho' => '148/210', 'dg_cartaz' => 39, 'dg_motivo' => 333),
        'PADARIA' => array('tamanho' => '148/210', 'dg_cartaz' => 39, 'dg_motivo' => 333),
        'ROTISSERIE' => array('tamanho' => '148/210', 'dg_cartaz' => 39, 'dg_motivo' => 333),

        'CERVEJAS' =>   array('tamanho' => '105/148', 'dg_cartaz' => 213, 'dg_motivo' => 473),
        'CIGARROS' =>   array('tamanho' => '105/148', 'dg_cartaz' => 213, 'dg_motivo' => 473),
        'VINHOS' =>     array('tamanho' => '105/148', 'dg_cartaz' => 213, 'dg_motivo' => 473),
        'ALCOOLICOS' => array('tamanho' => '105/148', 'dg_cartaz' => 213, 'dg_motivo' => 473),

        'DEFAULT' => array('tamanho' => '105/148', 'dg_cartaz' => 50, 'dg_motivo' => 422)
    );

    const FEIRAO = array(
        'FLV' => array('tamanho' => '210/148', 'dg_cartaz' => 89, 'dg_motivo' => 345)
    );

    const VEMQUETEM = array(
        'ACOUGUE' => array('tamanho' => '148/210', 'dg_cartaz' => 101, 'dg_motivo' => 218),
        'PADARIA' => array('tamanho' => '148/210', 'dg_cartaz' => 101, 'dg_motivo' => 218),
        'ROTISSERIE' => array('tamanho' => '148/210', 'dg_cartaz' => 101, 'dg_motivo' => 218),

        'CERVEJAS' =>   array('tamanho' => '105/148', 'dg_cartaz' => 251, 'dg_motivo' => 475),
        'CIGARROS' =>   array('tamanho' => '105/148', 'dg_cartaz' => 251, 'dg_motivo' => 475),
        'VINHOS' =>     array('tamanho' => '105/148', 'dg_cartaz' => 251, 'dg_motivo' => 475),
        'ALCOOLICOS' => array('tamanho' => '105/148', 'dg_cartaz' => 251, 'dg_motivo' => 475),

        'FLV' => array('tamanho' => '210/148', 'dg_cartaz' => 105, 'dg_motivo' => 222),

        'DEFAULT' => array('tamanho' => '105/148', 'dg_cartaz' => 214, 'dg_motivo' => 475)
    );

    const CLUBE = array(
        'CERVEJAS' =>   array('tamanho' => '105/148', 'dg_cartaz' => 277, 'dg_motivo' => 424),
        'CIGARROS' =>   array('tamanho' => '105/148', 'dg_cartaz' => 277, 'dg_motivo' => 424),
        'VINHOS' =>     array('tamanho' => '105/148', 'dg_cartaz' => 277, 'dg_motivo' => 424),
        'ALCOOLICOS' => array('tamanho' => '105/148', 'dg_cartaz' => 277, 'dg_motivo' => 424),

        'DEFAULT' => array('tamanho' => '105/148', 'dg_cartaz' => 211, 'dg_motivo' => 424)
    );

    const IDEVER = array(
        'CERVEJAS' =>   array('tamanho' => '105/148', 'dg_cartaz' => 276, 'dg_motivo' => 429),
        'CIGARROS' =>   array('tamanho' => '105/148', 'dg_cartaz' => 276, 'dg_motivo' => 429),
        'VINHOS' =>     array('tamanho' => '105/148', 'dg_cartaz' => 276, 'dg_motivo' => 429),
        'ALCOOLICOS' => array('tamanho' => '105/148', 'dg_cartaz' => 276, 'dg_motivo' => 429),

        'DEFAULT' => array('tamanho' => '105/148', 'dg_cartaz' => 69, 'dg_motivo' => 429)
    );


    public static function get($promocao, $secao, $grupo)
    {
        $constants = constant(self::class . "::$promocao") ?? constant(self::class . "::DEFAULT");
        foreach ($constants as $key => $value) {
            if (stripos($key, $secao) !== false || stripos($key, $grupo) !== false) {
                return $value;
            }
        }
        return $constants['DEFAULT'];
    }
}
