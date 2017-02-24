<?php
namespace Flexio\Modulo\Agentes\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Agentes\Models\Agentes;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

class GuardarAgentesEstados{
    protected $request;
    protected $session;
    protected $disparador;


    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->disparador = new \Illuminate\Events\Dispatcher();
    }

    function guardar(){
          $campos = FormRequest::data_formulario($this->request->input('campo'));
          $ids = $campos['ids'];
          print_r($ids);
          $empresa = $this->session->empresaId();
          $campo = ['estado'=>$campos['estado']];
          $Agentes = Agentes::where(function($query)use($empresa,$ids){
              $query->where('id_empresa', $empresa)
                    ->whereIn('id',$ids);
                })->get();

            return Capsule::transaction(function() use($campo, $Agentes){
              return $Agentes->map(function($ant) use($campo){
                  $ant->update($campo);
                  return $ant;
              });
        });
    }
}
