<?php

namespace Flexio\Modulo\Ajustadores\Repository;

use Flexio\Modulo\Ajustadores\Models\Ajustadores;

class AjustadoresRepository {

    public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        //filtros
        $ajustadores = Ajustadores::deEmpresa($clause["empresa_id"]);

        //Si existen variables de orden
        if ($sidx != 'estado') {
            $ajustadores->orderBy('estado', 'ASC');
        }
        if ($sidx != NULL && $sord != NULL) {
            $ajustadores->orderBy($sidx, $sord);
        }
        //Si existen variables de limite	

        return $ajustadores->get();
    }

    public function listar_ajustadores($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {

        $query = Ajustadores::with(array('creadopor' => function($query) use($clause, $sidx, $sord) {
                        if (!empty($sidx) && preg_match("/cargo/i", $sidx)) {
                            $query->orderBy("nombre", $sord);
                        }
                    }));

        if ($clause != NULL && !empty($clause) && is_array($clause)) {
            foreach ($clause AS $field => $value) {

                //verificar si valor es array
                if (is_array($value)) {

                    $query->where($field, $value[0], $value[1]);
                } else {
                    $query->where($field, '=', $value);
                }
            }
        }
        //Si existen variables de orden
        if ($sidx != NULL && $sord != NULL) {
            if (!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)) {
                $query->orderBy($sidx, $sord);
            }
        }

        //Si existen variables de limite
        if ($limit != NULL)
            $query->skip($start)->take($limit);

        return $query->get();
    }

    public function consultaRuc($ruc) {
        $ruc_dev = Ajustadores::where('ruc', '=', $ruc);
        return $ruc_dev->get();
    }

    public function consultaRucEmp($ruc, $empresa) {
        $ruc_dev = Ajustadores::where('ruc', '=', $ruc)->where('empresa_id', '=', $empresa);
        return $ruc_dev->get();
    }

    public function verAjustadores($id) {
        //filtros
        $ajustadores = Ajustadores::where("uuid_ajustadores", $id);

        return $ajustadores->first();
    }

}
