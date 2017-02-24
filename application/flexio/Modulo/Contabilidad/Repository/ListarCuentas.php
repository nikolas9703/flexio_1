<?php

namespace Flexio\Modulo\Contabilidad\Repository;

use Flexio\Modulo\Contabilidad\Models\Cuentas as Cuentas;
use Illuminate\Database\Capsule\Manager as Capsule;

class ListarCuentas
{
    protected static $cuentas_contable = array();
    protected $cuenta;

    public function __construct()
    {
        $this->cuenta = new Cuentas();
    }

    public function find($id)
    {
        return Cuentas::find($id);
    }

    public function listar_cuentas($clause = array())
    {
        self::$cuentas_contable = array();
        $empresa_id = $clause['empresa_id'];
        $clause['padre_id'] = $clause['padre_id'] ? : 0;

        $result_search = $this->cuenta->where(function ($query) use ($clause) {
            //$query->where($clause);
            if ($clause != null && !empty($clause) && is_array($clause)) {
                if(isset($clause['padre_id']) && !empty($clause['padre_id'])){
                    unset($clause['nombre']);
                }else if (isset($clause['nombre']) && !empty($clause['nombre'])) {
                    unset($clause['padre_id']);
                }

                foreach ($clause as $field => $value) {
                    if ($field == 'id') {
                        continue;
                    }

                    //Concatenar Nombre y Apellido para busqueda
                    if ($field == 'nombre') {
                        $field = Capsule::raw("IF(nombre != '', nombre, '')");
                    }

                    //Verificar si el campo tiene el simbolo @ y removerselo.
                    if (preg_match('/@/i', $field)) {
                        $field = str_replace('@', '', $field);
                    }

                    //verificar si valor es array
                    if (is_array($value)) {
                        $query->where($field, $value[0], $value[1]);
                    } else {
                        $query->where($field, '=', $value);
                    }
                }//end foreach
            }//end if
        });

        return $result_search->get()->map(function($cuenta){
            return $this->newDataCuenta($cuenta);
        });
    }

    public function newDataCuenta($cuenta)
    {
        return array_merge(
            $cuenta->toArray(),
            [
                'is_padre' => $cuenta->is_padre
            ]
        );
    }
}
