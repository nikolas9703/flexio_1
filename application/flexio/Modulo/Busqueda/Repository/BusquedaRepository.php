<?php
namespace Flexio\Modulo\Busqueda\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Busqueda\Models\Busqueda;
use Flexio\Library\Util\FlexioSession;
class BusquedaRepository{
  function __construct(){
       $this->session = new FlexioSession;
  }
 public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null) {
       $busquedas = Busqueda::where('empresa_id',$this->session->empresaId())
       ->where('usuario_id',$this->session->usuarioId())
       ->where('modulo',$clause["busquedable_type"]);
       if($sidx!=NULL && $sord!=NULL){$busquedas->orderBy($sidx, $sord);}
       if($limit!=NULL){$busquedas->skip($start)->take($limit);}
       return $busquedas->get();
    }
	function find($id) {
		return Busqueda::find($id);
	}
	function create($created) {
    $created['busqueda']['usuario_id'] = $this->session->usuarioId();
    $created['busqueda']['campos'] = json_encode($created['campo']);
    $created['busqueda']['empresa_id'] =   $this->session->empresaId();
  //  $created['busqueda']['modulo'] =    $this->session->uri()->segment(1);
    $Busqueda = Busqueda::create($created['busqueda']);
		return $Busqueda;
	}
	public function borrar($id) {
    $Busqueda = $this->find($id);
    return $Busqueda->delete();
	}
}
