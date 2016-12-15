<?php
namespace Flexio\Modulo\Cliente\HttpRequest;
use Flexio\Modulo\Cliente\SaveMany\CorreosSync;
use Flexio\Modulo\Cliente\SaveMany\CorreosTransform;
use Flexio\Modulo\Cliente\SaveMany\TelefonosSync;
use Flexio\Modulo\Cliente\SaveMany\TelefonosTransform;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\Cliente\Models\Asignados;
use Flexio\Modulo\Cliente\Models\Telefonos;
use Flexio\Modulo\Cliente\Models\Correos;
use Flexio\Modulo\Cliente\SaveMany\AsignadosSync;
use Flexio\Modulo\Cliente\SaveMany\AsignadosTransform;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Library\Util\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Flexio\Modulo\CentroFacturable\SaveMany\CentroFacturableTransform;
use Flexio\Modulo\CentroFacturable\SaveMany\CentroFacturableSync;
use Flexio\Modulo\Comentario\Models\Comentario;


class ClienteRequest {

  function __construct() {
    $this->request = Request::capture();
  }

  public function rules() {

  }

  function guardar($empresa_id, $codigo) {
    //tiene la data del usuario
    
    $campos = FormRequest::data_formulario($this->request->input('campo'));

    $centros = FormRequest::array_filter_dos_dimenciones($this->request->input('centros'));
    $asignados = FormRequest::array_filter_dos_dimenciones($this->request->input('asignados'));
      $telefonos = FormRequest::array_filter_dos_dimenciones($this->request->input('telefonos'));
      $correos = FormRequest::array_filter_dos_dimenciones($this->request->input('correos'));
    //$adiccion = $this->formatoCedula($campos['tipo_identificacion']); //Codigo Viejo
    $campos['credito_limite'] = str_replace(",", "", $campos['credito_limite']);
    $adiccion = (!empty($campos['tipo_identificacion']))?$this->formatoCedula($campos['tipo_identificacion']):array();
    $cp_id = $this->request->input('id_cp');
    $campos = array_merge($campos,$adiccion);
     $centros = $this->setEmpresa($centros, $empresa_id);
     return Capsule::transaction(function() use($campos, $centros, $empresa_id, $codigo, $asignados, $cp_id, $telefonos, $correos){


      if(!isset($campos['uuid'])){
          $identificacion = $campos['identificacion'];
          $datos = array('identificacion' => $identificacion,
              'empresa_id' => $empresa_id);
          //card para remover duplicacion de identificacion
          //$var_ind = Cliente::getIdentificacionClientes($datos)->count();
          //if ($var_ind > 0){
            //  return 'con_identificacion';

          //}else{
              $campos['empresa_id'] = $empresa_id;
              $campos['codigo'] = $codigo;
              $cliente = Cliente::registrar();
              $cliente->fill($campos)->save();

              $comentarios_duplicados =  $this->buscar_comentarios($cp_id);
              $cliente->comentario_timeline()->saveMany($comentarios_duplicados);
          //}

       }else{
        $cliente = Cliente::where('uuid_cliente',hex2bin($campos['uuid']))->first();
         // dd($campos);
          $cliente->nombre = $campos['nombre'];
          $cliente->tipo_identificacion = $campos['tipo_identificacion'];
          $cliente->credito_limite = $campos['credito_limite'];
          $cliente->toma_contacto_id = $campos['toma_contacto_id'];
          $cliente->comentario = $campos['comentario'];
          $cliente->identificacion = $campos['identificacion'];
          $cliente->tipo = $campos['tipo'];
          $cliente->categoria = $campos['categoria'];
          $cliente->estado = $campos['estado'];

          $cliente->save();
          //$cliente->update($campos);
         (new CentroFacturableSync)->sync(array_pluck($centros, 'id'),$cliente->centro_facturable->lists('id')->toArray());
         (new AsignadosSync)->sync(array_pluck($asignados, 'id'),$cliente->clientes_asignados->lists('id')->toArray());
          (new TelefonosSync)->sync(array_pluck($telefonos, 'id'),$cliente->telefonos_asignados->lists('id')->toArray());
          (new CorreosSync)->sync(array_pluck($correos, 'id'),$cliente->correos_asignados->lists('id')->toArray());
      }

      $centro_facturable = new CentroFacturableTransform;
      $items = $centro_facturable->crearInstancia($centros);
      $cliente->centro_facturable()->saveMany($items);

      $asignados_transform = new AsignadosTransform;
      $asig = $asignados_transform->crearInstancia($asignados);
      $cliente->clientes_asignados()->saveMany($asig);

         $telefonos_tranf = new TelefonosTransform;
         $tel = $telefonos_tranf->crearInstancia($telefonos);
         $cliente->telefonos_asignados()->saveMany($tel);

         $correos_tranf = new CorreosTransform;
         $cor = $correos_tranf->crearInstancia($correos);
         $cliente->correos_asignados()->saveMany($cor);
      return $cliente;
    });


  }

  function formatoCedula($method) {
    return call_user_func(array($this, $method));
  }

  function natural() {
    $natural = $this->request->input('natural');
    $letra = $natural['letra'];
      if ($natural['letra'] == '0') {
        $cedula = $natural['provincia'] . "-" . $natural['tomo'] . "-" . $natural['asiento'];
      }elseif ($natural['letra'] == 'E' || $natural['letra'] == 'N' || $natural['letra'] == 'PE' || $natural['letra'] == 'PI') {
        $cedula = $natural['letra'] . "-" . $natural['tomo'] . "-" . $natural['asiento'];
      if ($natural['letra'] == 'PI') $cedula = $natural['provincia'] . $natural['letra'] . "-" . $natural['tomo'] . "-" . $natural['asiento'];
      }
    return ['identificacion'=>$cedula, 'letra'=>$letra];
  }

  function pasaporte() {
    $pasaporte = $this->request->input('pasaporte');
    return ['identificacion' => $pasaporte['pasaporte']];
  }

  function juridico() {
    $juridico = $this->request->input('juridico');
    $cedula = $juridico['tomo'] . "-" . $juridico['folio'] . "-" . $juridico['asiento'] . "-" . $juridico['verificador'];
    return ['identificacion' => $cedula];
   }

  function setAsignados($asignados = array()) {
 	  	$asignados_array = [];
	  	if(count($asignados)>0){
	  		foreach($asignados as  $asignado){
	  			$fieldset = array(
	  					"id"=>$asignado['id'],
	  					"usuario_id"=>$asignado['usuario_id'],
	  					"linea_negocio"=>$asignado['linea_negocio']
	  			);
 	  			$asignados_array[] 	 = new Asignados($fieldset);

	  		}

	  	}
	  	return $asignados_array;
  	}


  	function buscar_comentarios($id) {
  		$comentariosNuevos = [];
   		$comentarios = Comentario::where('comentable_id', '=', $id)->where('comentable_type', '=', 'Flexio\Modulo\ClientesPotenciales\Models\ClientesPotenciales')->get();

  		$comentariosNuevos = $comentarios->each(function ($item, $key ) {
  			$copia_comentario = $item->replicate();
  			$copia_comentario->save();
  			$copia_comentario->comentable_type = 'Flexio\Modulo\ClientesPotenciales\Models\Clientes';
  			unset($copia_comentario->id);
  			unset($copia_comentario->comentable_id);
  			$comentarios_nuevos[] = $copia_comentario;
  			return $comentarios_nuevos;
  		});
  		return  $comentariosNuevos;
  	}
  function setEmpresa($centros, $empresa_id) {
    foreach($centros as $key=>$centro){
      $centros[$key]['empresa_id'] = $empresa_id;
    }
    return $centros;
  }
}
