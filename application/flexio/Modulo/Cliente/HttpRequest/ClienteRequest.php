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
use Flexio\Modulo\Agentes\Models\AgentesRamos;
use Flexio\Modulo\Ramos\Models\Ramos;


class ClienteRequest {

  function __construct() {
    $this->request = Request::capture();
  }

  public function rules() {

  }

    public function guardar($empresa_id, $codigo, $usuario_id) {

        $campos = FormRequest::data_formulario($this->request->input('campo'));
        $centros = FormRequest::array_filter_dos_dimenciones($this->request->input('centro_facturacion'));
        $asignados = FormRequest::array_filter_dos_dimenciones($this->request->input('asignados'));
        $telefonos = FormRequest::array_filter_dos_dimenciones($this->request->input('telefonos'));
        $correos = FormRequest::array_filter_dos_dimenciones($this->request->input('correos'));

        $camposagente = FormRequest::data_formulario($this->request->input('agentesCliente'));
        $camposramos = FormRequest::data_formulario($this->request->input('ramos_agentes_h'));
        $camposporcentajes = FormRequest::data_formulario($this->request->input('porcentajes_agentes'));

        print_r($camposagente);
        print_r("<br>");
        print_r($camposramos);
        print_r("<br>");
        print_r($camposporcentajes);
        print_r("<br>");

        $cp_id = $this->request->input('id_cp');
        $centros = $this->setEmpresa($centros, $empresa_id);
        return Capsule::transaction(function() use($campos, $centros, $empresa_id, $codigo, $asignados, $cp_id, $telefonos, $correos, $usuario_id){

            if(!isset($campos['id'])){

                //se pueden registrar varios clientes sin identificacion
                //$clause = ['identificacion' => $campos['identificacion'], 'empresa_id' => $empresa_id];
                //$cliente_duplicado = Cliente::getIdentificacionClientes($clause)->count();
                //if ($cliente_duplicado > 0)return 'con_identificacion';

                $campos['empresa_id'] = $empresa_id;
                $campos['creado_por'] = $usuario_id;
                $campos['codigo'] = $codigo;
                $cliente = Cliente::registrar();
                $cliente->fill($campos)->save();

                $comentarios_duplicados =  $this->buscar_comentarios($cp_id);
                $cliente->comentario_timeline()->saveMany($comentarios_duplicados);

                foreach ($camposagente as $key => $value) {
                  $agente = $value;
                  foreach ($camposramos[$key] as $key1 => $value1) {
                    $ramo = $value1;
                    $ramo = trim($ramo, ",");
                    $r = explode(",", $ramo);
                    foreach ($r as $vramo) {
                      $porcentaje = $camposporcentajes[$key][$key1];
                      //echo $agente."-".$vramo."-".$porcentaje."%<br>";
                      $agts = array();
                      $agts['id_cliente'] = $cliente->id;
                      $agts['id_agente'] = $agente;
                      $agts['id_ramo'] = $vramo;
                      $agts['participacion'] = $porcentaje;
                      $agtram = AgentesRamos::create($agts);
                    }            
                  }          
                }

            }else{

                $cliente = Cliente::find($campos['id']);
                $cliente->update(array_merge($campos,['creado_por' => $usuario_id]));
                (new CentroFacturableSync)->sync(array_pluck($centros, 'id'),$cliente->centro_facturable->lists('id')->toArray());
                (new AsignadosSync)->sync(array_pluck($asignados, 'id'),$cliente->clientes_asignados->lists('id')->toArray());
                (new TelefonosSync)->sync(array_pluck($telefonos, 'id'),$cliente->telefonos_asignados->lists('id')->toArray());
                (new CorreosSync)->sync(array_pluck($correos, 'id'),$cliente->correos_asignados->lists('id')->toArray());

                $cli = AgentesRamos::where("id_cliente", $campos['id'])->delete();
                foreach ($camposagente as $key => $value) {
                  $agente = $value;
                  foreach ($camposramos[$key] as $key1 => $value1) {
                    $ramo = $value1;
                    $ramo = trim($ramo, ",");
                    $r = explode(",", $ramo);
                    foreach ($r as $vramo) {
                      $porcentaje = $camposporcentajes[$key][$key1];
                      //echo $agente."-".$vramo."-".$porcentaje."%<br>";
                      if ($vramo == "todos") {
                        $nramos = Ramos::where("empresa_id", $empresa_id)->get();
                        foreach ($nramos as $v) {
                          $agts = array();
                          $agts['id_cliente'] = $cliente->id;
                          $agts['id_agente'] = $agente;
                          $agts['id_ramo'] = $v->id;
                          $agts['participacion'] = $porcentaje;
                          $agtram = AgentesRamos::create($agts);
                        }
                      }else{
                        $agts = array();
                        $agts['id_cliente'] = $cliente->id;
                        $agts['id_agente'] = $agente;
                        $agts['id_ramo'] = $vramo;
                        $agts['participacion'] = $porcentaje;
                        //print_r($agts);
                        //print_r("<br>");
                        $agtram = AgentesRamos::create($agts);
                      }                      
                    }            
                  }          
                }

            }
            exit();

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
