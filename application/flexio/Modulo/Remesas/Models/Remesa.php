<?php 

namespace Flexio\Modulo\Remesas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Empresa\Models\Empresa;

class Remesa extends Model
{
  protected $table = 'seg_remesas';
  protected $fillable = ['id','uuid_remesa','remesa','fecha', 'aseguradora_id', 'monto', 'recibos_remesados' ,'poliza', 'usuario', 'estado',  'empresa_id', 'forma_pago', 'id_banco', 'numero_cheque', 'creado_por', 'fecha_desde', 'fecha_hasta', 'ramos_id', 'created_at', 'updated_at'];
  protected $guarded = ['id'];
  public $timestamps = false;


  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_remesa' => Capsule::raw("ORDER_UUID(uuid())")
      )), true);
    parent::__construct($attributes);
  }

    /**
     * Conteo de las remesas existentes
     *
     * @return [array] [description]
     */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){


      $remesas = self::LeftJoin('seg_aseguradoras as asg','asg.id','=','seg_remesas.aseguradora_id')
      ->LeftJoin('usuarios as us','us.id','=','seg_remesas.usuario')
      ->LeftJoin('pol_polizas as pol' ,'pol.id','=','seg_remesas.poliza')
      ->LeftJoin('cob_cobros as cob','cob.num_remesa','=','seg_remesas.remesa')
      ->select('seg_remesas.*','asg.nombre',Capsule::raw('concat(us.nombre," ",us.apellido) as fullname'),'pol.numero', 'pol.uuid_polizas',Capsule::raw('count(cob.num_remesa) as cantidadRecibos'))
      ->groupBy('seg_remesas.id')
      ->where(function($query) use($clause,$sidx,$sord,$limit,$start){

       $query->where('seg_remesas.empresa_id','=',$clause['empresa_id']);

       if(isset($clause['remesa']))$query->where('seg_remesas.remesa','like' ,"%".$clause['remesa']."%");
       if(isset($clause['estado']))$query->whereIn('seg_remesas.estado',$clause['estado']);

       if(isset($clause['poliza']))$query->where('pol.numero','like' ,"%".$clause['poliza']."%");
       if(isset($clause['aseguradora']))$query->whereIn('asg.id',$clause['aseguradora']);
       if(isset($clause['usuario']))$query->whereIn('us.id',$clause['usuario']);
       if(isset($clause['id']))$query->whereIn('seg_remesas.id',$clause['id']);

       if($limit!=NULL) $query->skip($start)->take($limit);
     });
      if(isset($clause['recibo'])){

        $recibo = $clause['recibo']; 
        if($recibo=="ZERO"){
          $recibo = 0;
        }
        $remesas->having('cantidadRecibos', '=',$recibo);

      }

      if($sidx=="nombre"){

        $remesas->orderByRaw('FIELD(seg_remesas.estado,"En Proceso","Pagada","Anulado") ');

      }elseif($sidx!=NULL && $sord!=NULL){

        $remesas->orderBy($sidx, $sord);
      }

      return $remesas->get();
    }



    public static function findByUuid($uuid){
      return self::where('uuid_remesa',hex2bin($uuid))->first();
    }

    public function datosEmpresa(){
      return $this->hasOne(Empresa::class, 'id', 'empresa_id');
    }
  }
