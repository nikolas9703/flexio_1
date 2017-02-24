<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Polizas\Models\Polizas;
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;

class GuardarPolizasEstado 
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
		$Agentes = Polizas::where(function($query)use($empresa,$ids){
		$query	->where('empresa_id', $empresa)
				->where('id',$ids);
		})->get();

		return Capsule::transaction(function() use($campo, $Agentes){
			return $Agentes->map(function($ant) use($campo){
				$ant->update($campo);
				return $ant;
			});
		});
		
    }
    
}


    