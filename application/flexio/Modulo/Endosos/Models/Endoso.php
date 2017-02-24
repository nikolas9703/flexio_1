<?php 

namespace Flexio\Modulo\Endosos\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Empresa\Models\Empresa;
use Flexio\Modulo\Cliente\Models\Cliente;
use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
use Flexio\Modulo\Ramos\Models\Ramos;
use Flexio\Modulo\Polizas\Models\Polizas;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\SegCatalogo\Models\SegCatalogo;

class Endoso extends Model
{
  protected $table = 'end_endosos';
  protected $fillable = ['id','uuid_endoso','endoso','cliente_id','aseguradora_id','usuario','empresa_id','id_ramo','id_poliza','fecha_creacion','tipo','motivo','estado','modifica_prima','fecha_efectividad','descripcion']; //,'created_at','updated_at'
  protected $guarded = ['id'];
  public $timestamps = false;


  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_endoso' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

  public static function getLastCodigo($clause=[]){
        $count = self::where($clause)->count();
        return sprintf('END'.$clause['empresa_id'].'%06d', $count + 1);
    }
    
  public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

    $endosos = self::where(['end_endosos.empresa_id' => $clause['empresa_id']])
      ->where(function($query) use ($clause,$sidx,$sord,$limit,$start){

        if(isset($clause['endoso'])){
          $query->where('endoso', 'like', '%'.$clause['endoso'].'%');
        }
        if(isset($clause['cliente_id'])){
          $query->where('cliente_id', $clause['cliente_id']);
        }
        if(isset($clause['aseguradora_id'])){
          $query->where('aseguradora_id', $clause['aseguradora_id']);
        }
        if(isset($clause['id_ramo'])){
          $query->whereIn('id_ramo', $clause['id_ramo']);
        }
        if(isset($clause['tipo'])){
          $query->where('tipo', $clause['tipo']);
        }
        if(isset($clause['motivo'])){
          $query->where('motivo', $clause['motivo']);
        }
        if(isset($clause['fecha_inicio']) && isset($clause['fecha_final']) ){
          $query->whereBetween('fecha_creacion', array($clause['fecha_inicio'],$clause['fecha_final']));
        }elseif(isset($clause['fecha_inicio']) ){
          $query->where('fecha_creacion','>=',$clause['fecha_inicio']);
        }elseif(isset($clause['fecha_final']) ){
          $query->where('fecha_creacion','<=',$clause['fecha_final']);
        }
        if(isset($clause['estado'])){
          $query->where('estado', $clause['estado']);
        }
        if(isset($clause['id_poliza'])){
          $query->where('id_poliza',$clause['id_poliza']);
        }

        if($limit != NULL) $query->skip($start)->take($limit);
    })->with(array('cliente','aseguradora','ramos','polizas'));
  
    if($sidx == NULL){
      $endosos->orderByRaw('FIELD(end_endosos.estado,"Pendiente","En Trámite","Aprobado","Rechazado","Cancelado")');
      $endosos->orderBy('end_endosos.fecha_creacion','desc');
      $endosos->orderBy('end_endosos.endoso','desc');
    }

    if($sidx != NULL && $sord != NULL){
      if($sidx == "cliente_id"){
        $endosos->LeftJoin('cli_clientes', 'end_endosos.cliente_id', '=', 'cli_clientes.id');
        $endosos->orderBy('cli_clientes.nombre', $sord);
      }elseif($sidx == "aseguradora_id"){
        $endosos->LeftJoin('seg_aseguradoras', 'end_endosos.aseguradora_id', '=', 'seg_aseguradoras.id');
        $endosos->orderBy('seg_aseguradoras.nombre', $sord);
      }elseif($sidx == "id_ramo"){
        $endosos->LeftJoin('seg_ramos', 'end_endosos.id_ramo', '=', 'seg_ramos.id');
        $endosos->orderBy('seg_ramos.nombre', $sord);
      }elseif($sidx == "id_poliza"){
        $endosos->LeftJoin('pol_polizas', 'end_endosos.id_poliza', '=', 'pol_polizas.id');
        $endosos->orderBy('pol_polizas.numero', $sord);
      }else{
        $endosos->orderBy($sidx,$sord);
      }
    }

    

    return $endosos->select('end_endosos.*')->get();
  }

  public function cliente(){
    return $this->hasOne(Cliente::class , 'id', 'cliente_id');
  }

  public function aseguradora(){
    return $this->hasOne(Aseguradoras::class , 'id', 'aseguradora_id');
  }

  public function ramos(){
    return $this->hasOne(Ramos::class , 'id', 'id_ramo');
  }

  public function polizas(){
    return $this->hasOne(Polizas::class, 'id', 'id_poliza');
  }

  public function motivos(){
    return $this->hasOne(SegCatalogo::class, 'id', 'motivo');
  }

  public static function findByUuid($uuid){
    return self::where('uuid_endoso',hex2bin($uuid))->first();
  }

  public function datosEmpresa(){
    return $this->hasOne(Empresa::class, 'id', 'empresa_id');
  }
  function documentos() {
    return $this->morphMany(Documentos::class, 'documentable');
  }

}
