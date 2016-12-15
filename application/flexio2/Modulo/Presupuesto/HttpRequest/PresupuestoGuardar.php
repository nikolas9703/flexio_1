<?php

namespace Flexio\Modulo\Presupuesto\HttpRequest;

use Flexio\Library\Util\FormRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Presupuesto\Models\Presupuesto;
use Flexio\Modulo\Presupuesto\Models\PresupuestoHistorial;

class PresupuestoGuardar{

  protected $request;
  protected $empresa_id;
  protected $codigo;
  protected $usuario_id;

  function __construct($empresa_id,$codigo,$usuario_id){
    $this->request = Request::capture();
    $this->empresa_id = $empresa_id;
    $this->codigo = $codigo;
    $this->usuario_id = $usuario_id;
  }

  function procesarGuardar(){
    //captuta el post para el presupuesto
    $requestPresupuesto =  FormRequest::data_formulario($this->request->input('presupuesto'));
    $requestPresupuesto['empresa_id'] = $this->empresa_id;
    $requestPresupuesto['usuario_id'] = $this->usuario_id;
    //se adjunta el tipo porque fue desabilidado en el UI
    if(isset($requestPresupuesto['id'])){
      $presupuesto = Presupuesto::find($requestPresupuesto['id']);
      $requestPresupuesto['tipo'] = $presupuesto->tipo;
    }

    //captura el post para los itemPresupuesto
    $itemPresupuesto = $this->request->input('presupuesto_cuentas');
    // clase para darle forma a la data items del presupuesto
    $cuentasPresupuestoObj = new FormatoItemsPresupuesto;
    $cuentasPresupuesto = $cuentasPresupuestoObj->obtenerData($itemPresupuesto, $requestPresupuesto);

    if(!method_exists($this, $requestPresupuesto['tipo'])){
       throw new \Exception("La funcion no existe para esta clase " . __CLASS__);
    }

    return call_user_func_array([$this, $requestPresupuesto['tipo'] ], [$requestPresupuesto,$cuentasPresupuesto]);
  }

  function periodo($requestPresupuesto, $cuentasPresupuesto){
    $presupuesto = null;
    list($mes, $year) = explode("-",$requestPresupuesto['inicio']);
    $fecha_inicio =  Carbon::createFromDate($year, $mes, 1)->timezone('America/Panama');
    $requestPresupuesto['fecha_inicio'] = $fecha_inicio;

    if(isset($requestPresupuesto['id'])){
      $presupuesto = $this->actualizar($requestPresupuesto, $cuentasPresupuesto);
    }else{
      $requestPresupuesto['codigo'] = $this->codigo;
      $presupuesto = $this->crear($requestPresupuesto, $cuentasPresupuesto);
    }

    return $presupuesto;

  }
  function avance($requestPresupuesto, $cuentasPresupuesto){
    $presupuesto = null;
    $requestPresupuesto['fecha_inicio'] = Carbon::createFromFormat('d/m/Y',$requestPresupuesto['fecha_inicio']);
    if(isset($requestPresupuesto['id'])){
      $presupuesto = $this->actualizar($requestPresupuesto, $cuentasPresupuesto);
    }else{
      $requestPresupuesto['codigo'] = $this->codigo;
      $presupuesto = $this->crear($requestPresupuesto, $cuentasPresupuesto);
    }
    return $presupuesto;
  }

  function crear($requestPresupuesto, $cuentasPresupuesto){

    return Capsule::transaction(function() use($requestPresupuesto,$cuentasPresupuesto){
      $presupuesto = Presupuesto::registrar();
      $presupuesto->fill($requestPresupuesto)->save();
      $modelCentroPresupuesto = (new PresupuestoCentro)->crearInstancia($cuentasPresupuesto);

      $presupuesto->lista_presupuesto()->saveMany($modelCentroPresupuesto);
      $this->addHistorial($presupuesto);
      return $presupuesto;
    });

  }

  function actualizar($requestPresupuesto, $cuentasPresupuesto){
    return Capsule::transaction(function() use($requestPresupuesto,$cuentasPresupuesto){
      $presupuesto = Presupuesto::find($requestPresupuesto['id']);
      $presupuesto->update($requestPresupuesto);
      $modelCentroPresupuesto = (new PresupuestoCentro)->crearInstancia($cuentasPresupuesto);

      $presupuesto->lista_presupuesto()->saveMany($modelCentroPresupuesto);
      return $presupuesto;
    });
  }

  function addHistorial($presupuesto){
      $create = [
          'codigo' => $presupuesto->codigo,
          'usuario_id' => $presupuesto->usuario_id,
          'empresa_id' => $presupuesto->empresa_id,
          'presupuesto_id'=> $presupuesto->id,
          'descripcion' => 'Se creó el presupuesto'
      ];
      PresupuestoHistorial::create($create);
  }

}
