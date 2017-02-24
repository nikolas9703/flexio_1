<?php
namespace Flexio\Modulo\Solicitudes\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Flexio\Modulo\Usuarios\Models\Usuarios;
use Flexio\Modulo\Solicitudes\Models\Solicitudes;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Illuminate\Http\Request;

class GuardarEstadosBitacora extends Model
{
    protected $table        = 'seg_solicitudes_bitacora';    
    protected $fillable     = ['tipo', 'comentario', 'creado_por', 'fecha_creado'];
    protected $guarded      = ['id'];
    
    //scopes
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
          
          $tipo = $campos['tipo'];
          $estado = $campos['estado'];
          $estado_anterior = $campos['estado_anterior'];
          $motivo = $campos['motivo'];
          $solicitud = $campos['solicitud'];
          $comentario = "Estado Actual: ".$estado."<br>Estado Anterior: ".$estado_anterior."<br>Motivo: ".$motivo."<br>";
          $usuario = 0;
          $fecha_creado = date('Y-m-d H:i:s');          
          $campo = ['tipo'=>$tipo, 'comentario' => $comentario, 'creado_por' => $usuario, 'fecha_creado' => $fecha_creado];
          $Solicitud = GuardarEstadosBitacora::create($campo);
          return $Solicitud;          
    }
    
}