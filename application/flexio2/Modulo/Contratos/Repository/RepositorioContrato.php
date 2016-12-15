<?php
namespace Flexio\Modulo\Contratos\Repository;
use Flexio\Modulo\Contratos\Models\Contrato;

class RepositorioContrato{

    public $builder;

    function __construct(){
      $this->builder = (new Contrato)->newQuery();
    }

    function getContratos($empresa_id){

       $this->builder->where('empresa_id',$empresa_id);
       return $this;
    }

    function conId($id){
        $this->builder->where('id',$id);
        return $this;
    }

    function conClienteActivo(){
        $this->builder->whereHas('cliente',function($query){
          $query->where('estado','activo');
        });
        return $this;
    }

    function conFacturas(){
        $this->builder->whereHas('facturas', function($query){
          $query->whereIn('estado',['por_cobrar','cobrado_parcial']);
        });
        return $this;
    }

    function paraCobrar(){
        $this->builder->with(['facturas' =>function($query){
          $query->whereIn('estado',['por_cobrar','cobrado_parcial']);
        }]);
        return $this;
    }


    function fetch(){
      return $this->builder->get();
    }
}
