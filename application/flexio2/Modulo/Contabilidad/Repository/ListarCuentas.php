<?php namespace Flexio\Modulo\Contabilidad\Repository;
use Flexio\Modulo\Contabilidad\Models\Cuentas as Cuentas;
use Illuminate\Database\Capsule\Manager as Capsule;

class ListarCuentas{

  protected static $cuentas_contable = array();
  protected $cuenta;

  function __construct(){
    $this->cuenta = new Cuentas;
  }
public function find($id){
    return Cuentas::find($id);
}
  function listar_cuentas($clause = array()){
  	self::$cuentas_contable = array();
  	$empresa_id = $clause['empresa_id'];
  	$clause['padre_id'] = 0;

  	$result_search = $this->cuenta->where(function($query) use ($clause){
  		//$query->where($clause);
      if($clause!=NULL && !empty($clause) && is_array($clause))
  		{
        if (isset($clause['nombre'])){
            unset($clause['padre_id']);
        }

        foreach($clause AS $field => $value)
  			{
  				if($field == "id"){
  					continue;
  				}

  				//Concatenar Nombre y Apellido para busqueda
  				if($field == "nombre"){
  					$field = Capsule::raw("IF(nombre != '', nombre, '')");
  				}

  				//Verificar si el campo tiene el simbolo @ y removerselo.
  				if(preg_match('/@/i', $field)){
  					$field = str_replace("@", "", $field);
  				}

  				//verificar si valor es array
  				if(is_array($value)){
  					$query->where($field, $value[0], $value[1]);
  				}else{
  					$query->where($field, '=', $value);
  				}
  			}
  		}
  	});

  	$cuentas_contable = $result_search->get();

    $this->formatCuentas($cuentas_contable->toArray());
    return self::$cuentas_contable;
  }


  function formatCuentas($cuentas){

    foreach($cuentas as $cuenta){

      if(!empty($cuenta['cuentas_item'])){
        array_push(self::$cuentas_contable, $this->newDataCuenta($cuenta));
        $this->formatCuentas($cuenta['cuentas_item']);
      }else{
        array_push(self::$cuentas_contable,$this->newDataCuenta($cuenta));
      }
    }
  }

  function newDataCuenta($cuenta){
    return [
      'id'=> $cuenta['id'],
      'codigo'=> $cuenta['codigo'],
      'detalle'=> $cuenta['detalle'],
      'nombre'=> $cuenta['nombre'],
      'estado'=> $cuenta['estado'],
      'balance'=> $cuenta['balance'],
      'created_at'=> $cuenta['created_at'],
      'padre_id'=> $cuenta['padre_id'],
      'tipo_cuenta_id'=> $cuenta['tipo_cuenta_id'],
      'empresa_id'=> $cuenta['empresa_id'],
      'uuid_cuenta'=> $cuenta['uuid_cuenta'],
      'is_padre'=> $cuenta['is_padre']
    ];
  }

}
