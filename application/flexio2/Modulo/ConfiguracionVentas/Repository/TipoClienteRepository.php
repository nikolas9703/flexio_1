<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 6/10/16
 * Time: 1:32 PM
 */

namespace Flexio\Modulo\ConfiguracionVentas\Repository;

use Flexio\Modulo\ConfiguracionVentas\Models\TipoClientes as TipoClientes;

class TipoClienteRepository
{
    public static function findByUuid($uuid) {
        return TipoClientes::where('uuid_tipo',hex2bin($uuid))->first();
    }
    public static function exportar($clause = array()) {

        return TipoClientes::whereIn('uuid_tipo', $clause)->get();
    }

    public static function getCatalogoTipo($id_empresa = null) {
        return TipoClientes::where('id_empresa', $id_empresa)
            ->where('estado','activo')
            ->get(array('id', 'nombre'));
    }
}