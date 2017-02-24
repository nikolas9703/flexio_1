<?php
namespace Flexio\Modulo\FacturasSeguros\Repository;
use Flexio\Modulo\FacturasSeguros\Models\FacturaSeguro;

class FacturaSeguroRepositorio{

    public $builder;

    function __construct(){
      $this->builder = (new FacturaSeguro)->newQuery();
    }

    function getFacturas($empresa_id){

       $this->builder->select("fac_facturas.*","pol_polizas.numero as numero_poliza","pol_polizas.ramo as ramo_aso")->where('fac_facturas.empresa_id',$empresa_id)->whereIn('fac_facturas.estado',['por_cobrar','cobrado_parcial'])->leftJoin("pol_polizas","pol_polizas.id","=","fac_facturas.id_poliza")->groupBy("id_poliza")->orderBy("fac_facturas.cliente_id","asc")->orderBy("pol_polizas.ramo","asc")->orderBy("pol_polizas.numero","desc");;
	   
	   //$this->builder->select("fac_facturas.*","pol_polizas.numero as numero_poliza","pol_polizas.ramo as ramo_aso")->where('fac_facturas.empresa_id',$empresa_id)->where('fac_facturas.estado','por_cobrar')->where('fac_facturas.formulario','facturas_seguro')->join("pol_polizas","pol_polizas.id","=","fac_facturas.id_poliza")->groupBy("id_poliza")->orderBy("fac_facturas.cliente_id","asc")->orderBy("pol_polizas.ramo","asc")->orderBy("pol_polizas.numero","desc");
	   
       return $this;
    }

    function getPolizasFacturas($empresa_id){
		//$this->builder->where('empresa_id',$empresa_id)->where('estado','por_cobrar')->whereNotNull('id_poliza')->groupBy("id_poliza");
		$this->builder->select("fac_facturas.*","pol_polizas.numero as numero_poliza","pol_polizas.ramo as ramo_aso")->where('fac_facturas.empresa_id',$empresa_id)->whereIn('fac_facturas.estado',['por_cobrar','cobrado_parcial'])->where('fac_facturas.formulario','facturas_seguro')->join("pol_polizas","pol_polizas.id","=","fac_facturas.id_poliza")->groupBy("id_poliza")->orderBy("fac_facturas.cliente_id","asc")->orderBy("pol_polizas.ramo","asc")->orderBy("pol_polizas.numero","desc");
		return $this;
    }

    function getFacturasCliente($empresa_id,$id_cliente=""){
		$this->builder->select("fac_facturas.*","pol_polizas.numero as numero_poliza","pol_polizas.ramo as ramo_aso")->where('fac_facturas.empresa_id',$empresa_id)->whereIn('fac_facturas.estado',['por_cobrar','cobrado_parcial'])->where('fac_facturas.formulario','facturas_seguro')->where('fac_facturas.cliente_id',$id_cliente)->leftjoin("pol_polizas","pol_polizas.id","=","fac_facturas.id_poliza")->groupBy("id_poliza")->orderBy("fac_facturas.cliente_id","asc")->orderBy("pol_polizas.ramo","asc")->orderBy("pol_polizas.numero","desc");
		return $this;
    }

    function conId($id){
        $this->builder->where('id',$id);
        return $this;
    }

    function conUUID($uuid){
        $this->builder->where('uuid_factura', hex2bin($uuid));
        return $this;
    }

    function paraCobrar(){

        $this->builder->where(function($query){
            $query->where('estado','por_cobrar')
                  ->orWhere('estado','cobrado_parcial');
        });
        return $this;

    }

    function conClienteActivo(){
      $this->builder->whereHas('cliente',function($query){
        $query->where('estado','activo');
      });
      return $this;
    }

    function porCobrar(){
        $this->builder->where('estado','por_cobrar');
        return $this;
    }

    function cobradoParcial(){
        $this->builder->where('estado','cobrado_parcial');
        return $this;
    }

    function fetch(){
      return $this->builder->get();
    }


}
