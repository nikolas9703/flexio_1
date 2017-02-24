<?php
namespace Flexio\Modulo\Catalogos\Repository;

//models
use Flexio\Modulo\Catalogos\Models\Catalogo;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

class CatalogoRepository
{
    protected $session;

    function __construct(){
        $this->session = new FlexioSession;
    }

    private function _filtros($query, $clause)
    {
        if(isset($clause['modulo']) && !empty($clause['modulo'])){$query->whereModulo($clause['modulo']);}
        if(isset($clause['tipo']) && !empty($clause['tipo'])){$query->whereTipo($clause['tipo']);}
        if(isset($clause['activo']) && !empty($clause['activo'])){$query->whereActivo($clause['activo']);}
        if(isset($clause['etiqueta']) && !empty($clause['etiqueta'])){$query->whereEtiqueta($clause['etiqueta']);}
        if(isset($clause['id']) && !empty($clause['id']) && is_array($clause['id'])){$query->whereIn("id", $clause['id']);}
        if(isset($clause['con_acceso']) && is_numeric($clause['con_acceso'])){$query->where("con_acceso", $clause['con_acceso']);}
    }

    public function get($clause = [])
    {
        return Catalogo::where(function($query) use ($clause){
            $this->_filtros($query, $clause);
        })->get();
    }

    function crear($fieldset){

        if(empty($fieldset["etiqueta"])){
          $fieldset["etiqueta"] = str_replace(" ","_",strtolower($fieldset["valor"]));
        }

        return Capsule::transaction(function() use($fieldset){
            $response = Catalogo::create($fieldset);
            return $response;
        });
    }

    function actualizar($fieldset){

        if(empty($fieldset["activo"])){
          $fieldset["activo"] = "0";
        }
        if(empty($fieldset["con_acceso"])){
          $fieldset["con_acceso"] = "0";
        }
        return Capsule::transaction(function() use($fieldset){
            $catalogo = Catalogo::find($fieldset['id']);
            $response = $catalogo->update($fieldset);
            return $response;
        });
    }
    public function estado($etiqueta = null, $modulo = null){
        return Catalogo::where("etiqueta" , $etiqueta)
            ->where("modulo", $modulo)
            ->get();
    }
}
