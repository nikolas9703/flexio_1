<?php
namespace Flexio\Modulo\aseguradoras\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

class GuardarAseguradoraEstados{
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
          $Aseguradoras = Aseguradoras::where(function($query)use($empresa,$ids){
              $query->where('empresa_id', $empresa)
                    ->whereIn('id',$ids);
                })->get();

            return Capsule::transaction(function() use($campo, $Aseguradoras){
              return $Aseguradoras->map(function($ant) use($campo){
                  $ant->update($campo);
                  return $ant;
              });
        });
    }
}
