<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 21/2/17
 * Time: 3:10 PM
 */

namespace Flexio\Modulo\ConfiguracionContratos\Repository;

use Flexio\Modulo\ConfiguracionContratos\Models\TipoSubContratoCatalogo;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

class TipoSubContratoCatalogoRepository
{
    protected $session;

    function __construct(){
        $this->session = new FlexioSession;
    }

    function crear($fieldset){

        return Capsule::transaction(function() use($fieldset){
            $response = TipoSubContratoCatalogo::create($fieldset);

            return $response;
        });
    }
    function actualizar($fieldset){

        if(empty($fieldset["estado"])){
            $fieldset["estado"] = "0";
        }
        if(empty($fieldset["acceso"])){
            $fieldset["acceso"] = "0";
        }
        return Capsule::transaction(function() use($fieldset){
            $catalogo = TipoSubContratoCatalogo::find($fieldset['id']);
            $response = $catalogo->update($fieldset);
            return $response;
        });
    }

    public function get($clause = [])
    {
        return TipoSubContratoCatalogo::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        })->get();
    }

    private function _filtros($query, $clause)
    {
        if(isset($clause['empresa_id']) && !empty($clause['empresa_id'])){$query->whereEmpresa_id($clause['empresa_id']);}
        if(isset($clause['nombre']) && !empty($clause['nombre'])){$query->whereNombre($clause['nombre']);}
        if(isset($clause['estado']) && !empty($clause['estado'])){$query->whereEstado($clause['estado']);}
        if(isset($clause['id']) && !empty($clause['id']) && is_array($clause['id'])){$query->whereIn("id", $clause['id']);}
        if(isset($clause['acceso']) && is_numeric($clause['acceso'])){$query->where("acceso", $clause['acceso']);}
    }
}