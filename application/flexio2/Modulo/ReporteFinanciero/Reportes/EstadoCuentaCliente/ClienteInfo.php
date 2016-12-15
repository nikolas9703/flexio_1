<?php
namespace Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaCliente;
use Flexio\Modulo\Cliente\Models\Cliente as Cliente;
class ClienteInfo {

private $empresa_id;
private $cliente_id;
protected $cliente;


  function __construct($cliente_id, $empresa_id){
    $this->empresa_id = $empresa_id;
    $this->cliente_id = $cliente_id;
    $this->cliente = new Cliente;
  }

  function info(){
    $cliente =  $this->cliente->where(['empresa_id'=>$this->empresa_id, 'id'=>$this->cliente_id]);
    return $cliente;
  }
}
