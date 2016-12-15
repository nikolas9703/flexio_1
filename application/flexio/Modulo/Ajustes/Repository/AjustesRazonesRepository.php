<?php
namespace Flexio\Modulo\Ajustes\Repository;

use Flexio\Modulo\Ajustes\Models\AjustesRazones as AjustesRazones;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;

class AjustesRazonesRepository{
    
    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $ajustes_razones = AjustesRazones::deEmpresa($clause["empresa_id"]);
        
        //filtros
        $this->_filtros($ajustes_razones, $clause);
        
        if($sidx!=NULL && $sord!=NULL){$ajustes_razones->orderBy($sidx, $sord);}
        if($limit!=NULL){$ajustes_razones->skip($start)->take($limit);}
        return $ajustes_razones->get();
    }
    
    public function find($ajuste_razon_id)
    {
        return AjustesRazones::find($ajuste_razon_id);
    }
    
    public function findByUuid($uuid_ajuste_razon) {
        return AjustesRazones::findByUuid($uuid_ajuste_razon);
    }
    
    public function count($clause = array())
    {
        $ajustes_razones = AjustesRazones::deEmpresa($clause["empresa_id"]);
        
        //filtros
        $this->_filtros($ajustes_razones, $clause);
        
        return $ajustes_razones->count();
    }
    
    private function _filtros($ajustes_razones, $clause)
    {
        if(isset($clause["activas"])){$ajustes_razones->activas($clause["activas"]);}
    }
    
    private function _editar($post)
    {
        return (isset($post["uuid"]) and !empty($post["uuid"]));
    }
    
    public function save($post)
    {
        
        $ajuste_razon =  $this->_editar($post) ? $this->findByUuid($post["uuid"]) : new AjustesRazones();
        
        if(!$this->_editar($post))
        {
            $ajuste_razon->uuid_razon   = Capsule::raw("ORDER_UUID(uuid())");
            $ajuste_razon->estado_id    = '6';//Activo
            $ajuste_razon->empresa_id   = $post["empresa_id"];
            $ajuste_razon->created_by   = $post["usuario_id"];
        }
        
        $ajuste_razon->nombre       = $post["nombre"];
        $ajuste_razon->descripcion  = $post["descripcion"];
        

        return $ajuste_razon->save();
    }
    
    public function cambiar_estado($post)
    {
        $ajuste_razon =  $this->_editar($post) ? $this->findByUuid($post["uuid"]) : '';
        
        
        if(!$this->_editar($post))
        {
            return false;
        }
        
        $ajuste_razon->estado_id    = $post["estado"];
        
        return $ajuste_razon->save();
    }
    
}
