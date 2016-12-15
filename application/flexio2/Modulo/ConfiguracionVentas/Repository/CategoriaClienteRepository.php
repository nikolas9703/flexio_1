<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 6/10/16
 * Time: 1:31 PM
 */

namespace Flexio\Modulo\ConfiguracionVentas\Repository;

use Flexio\Modulo\ConfiguracionVentas\Models\CategoriaClientes as CategoriaClientes;

class CategoriaClienteRepository
{
    public static function findByUuid($uuid) {
        return CategoriaClientes::where('uuid_categoria',hex2bin($uuid))->first();
    }
    public static function exportar($clause = array()) {

        return CategoriaClientes::whereIn('uuid_categoria', $clause)->get();
    }
    public static function getCatalogoCategoria($id_empresa = null) {
         return CategoriaClientes::where('id_empresa', $id_empresa)
                                 ->where('estado','activo')
                                   ->get(array('id', 'nombre'));
    }
}