<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaProveedor;
use Flexio\Modulo\Proveedores\Models\Proveedores as Proveedor;
class ProveedorInfo {

private $empresa_id;
private $proveedor_id;
protected $proveedor;


  function __construct($proveedor_id, $empresa_id){
    $this->empresa_id = $empresa_id;
    $this->proveedor_id = $proveedor_id;
    $this->proveedor = new Proveedor;
  }

  function info(){
  $proveedor =  $this->proveedor->where(['id_empresa'=>$this->empresa_id, 'id'=>$this->proveedor_id]);
  return $proveedor;
  }
}
