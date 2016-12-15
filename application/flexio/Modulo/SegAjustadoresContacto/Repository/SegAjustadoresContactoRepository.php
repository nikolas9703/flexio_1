<?php

namespace Flexio\Modulo\SegAjustadoresContacto\Repository;

use Flexio\Modulo\SegAjustadoresContacto\Models\SegAjustadoresContacto;

class SegAjustadoresContactoRepository {

    public function listar($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        //filtros
        $contactos = SegAjustadoresContacto::DeAjustador($clause["ajustador_id"]);

        //Si existen variables de orden
        if ($sidx != 'estado') {
            $contactos->orderBy('estado', 'ASC');
        }
        if ($sidx != NULL && $sord != NULL) {
            $contactos->orderBy($sidx, $sord);
        }
        //Si existen variables de limite	

        return $contactos->get();
    }

    public function listar_contactos($clause = array(), $sidx = NULL, $sord = NULL, $limit = NULL, $start = NULL) {
        $query = SegAjustadoresContacto::with(array('creadopor' => function($query) use($clause, $sidx, $sord) {
                        if (!empty($sidx) && preg_match("/cargo/i", $sidx)) {
                            $query->orderBy("nombre", $sord, "estado", $sord);
                        }
                    }));
        if($clause!=NULL && !empty($clause) && is_array($clause))
        {
                foreach($clause AS $field => $value)
                {  
                        //verificar si valor es array
                        if(is_array($value)){
								if($field=='id')
								{
									$query->whereIn('id',$value);
								}
								else{
									$query->where($field, $value[0], $value[1]);
								}
                        }else{
                                $query->where($field, '=', $value);
                        }
                }
        }
        //Si existen variables de orden
        if ($sidx != NULL && $sord != NULL) {
            if (!preg_match("/(departamento|centro_contable)/i", $sidx)) {
                $query->orderBy($sidx, $sord);
            }
        }

        //Si existen variables de limite
        if ($limit != NULL)
            $query->skip($start)->take($limit);

        //var_dump($query->get());
        return $query->get();
    }

    //filtros
    public function verContacto($id) {
        //filtros
        $contacto = SegAjustadoresContacto::where("id", $id);

        return $contacto->first();
    }

    public function verContactoUiid($id) {
        //filtros
        $contacto = SegAjustadoresContacto::where("uuid_contacto", $id);

        return $contacto->first();
    }

    public function consultaEmail($email, $id) {
        $email_dev = SegAjustadoresContacto::where('email', '=', $email)
                ->where('ajustador_id', '=', $id);
        return $email_dev->get();
    }

    public function cambiarPrincipal($id) {
        //filtros
        $contactos = SegAjustadoresContacto::where('ajustador_id', $id)->update(array('contacto_principal' => 0));
        //$contacto = SegAseguradoraContacto::where("uuid_aseguradora", $id); 

        return $contactos;
    }

}
