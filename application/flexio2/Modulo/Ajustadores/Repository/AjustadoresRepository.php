<?php
namespace Flexio\Modulo\Ajustadores\Repository;
use Flexio\Modulo\Ajustadores\Models\Ajustadores;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class AjustadoresRepository{
    public function find($ajustadores_id) {
        return Ajustadores::find($ajustadores_id);
    }   
    public function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {
      $query = Ajustadores::where(function($query) use($clause){
          $query->where('empresa_id','=',$clause['empresa_id']);
          if(isset($clause['nombre']))$nombre = '%' . $clause['nombre'] . '%';
          if(isset($clause['nombre']))$query->where('nombre','LIKE' , $nombre);
          if(isset($clause['telefono']))$telefono = '%' . $clause['telefono'] . '%';
          if(isset($clause['telefono']))$query->where('telefono','LIKE' ,$telefono);
          if(isset($clause['email']))$email = '%' . $clause['email'] . '%';
          if(isset($clause['email']))$query->where('email','LIKE',$email);         
          if(isset($clause['ajustadores']))$query->whereIn('id',$clause['ajustadores']);         
      });
      if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
      if($limit!=NULL) $query->skip($start)->take($limit);
    return $query->get();
  }
  public static function findByUuid($uuid) {
        return Ajustadores::where('uuid_ajustadores', hex2bin($uuid))->first();
  }
  public static function buscar($ruc) {
        return Ajustadores::where('ruc', $ruc)->first();      
  }
}
