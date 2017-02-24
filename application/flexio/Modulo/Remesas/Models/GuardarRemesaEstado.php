<?php
namespace Flexio\Modulo\Remesas\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Remesas\Models\Remesa;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

class GuardarRemesaEstado{
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
          $empresa = $this->session->empresaId();
          $campo = ['estado'=>$campos['estado']];
          $Agentes = Remesa::where(function($query)use($empresa,$ids){
              $query->where('empresa_id', $empresa)
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