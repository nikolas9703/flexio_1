<?php
namespace Flexio\Modulo\Endosos\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Endosos\Models\Endoso;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Polizas\Models\PolizasBitacora;

class GuardarEndososEstado 
{
    protected $request;
    protected $session;
    protected $disparador;


    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->disparador = new \Illuminate\Events\Dispatcher();
    }

    public function guardar(){
      $campos = FormRequest::data_formulario($this->request->input('campo'));
      $ids = $campos['ids'];
      $empresa = $this->session->empresaId();
      $campo = ['estado'=>$campos['estado']];
      $Agentes = Endoso::where(function($query)use($empresa,$ids){
          $query->where('empresa_id', $empresa)
          ->whereIn('id',$ids);
      })->get();      
      $this->bitacoraPoliza($Agentes,$campo);
      return Capsule::transaction(function() use($campo, $Agentes){
        return $Agentes->map(function($ant) use($campo){
            $ant->update($campo);
            return $ant;
        });
      });
    }

    public function bitacoraPoliza($Agentes,$campo){
      foreach ($Agentes as $key => $value) {
        if($value['estado'] != $campo['estado']){
          $tipo = "Endosos_seguros";
          $fecha_creado = date('Y-m-d H:i:s');
          $comentario = "N° endoso: ".$value['endoso']."<br>Estado anterior: ".$value['estado']."<br>Estado actual: ".$campo['estado']."<br>Fecha cambio: ".date('d/m/Y');
          $comment = ['comentario'=> $comentario ,'usuario_id'=>$this->session->usuarioId(), 'comentable_id' =>$value['id_poliza'], 'comentable_type'=>$tipo, 'created_at'=>$fecha_creado, 'empresa_id'=>$this->session->empresaId()];
          $Bitacora = new PolizasBitacora;
          $Bitacora->create($comment);
        }
      }
    }
    
}


    