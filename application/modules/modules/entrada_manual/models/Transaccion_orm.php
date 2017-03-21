<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
//Use Flexio\Modulo\Transaccion\Models\SysTransaccion as SysTransaccion;
class Transaccion_orm extends Model
{
	protected $table = 'contab_transacciones';
	protected $primaryKey = 'id';
	protected $fillable = ['codigo','nombre','debito','credito','empresa_id','cuenta_id','centro_id'];
	protected $guarded = ['id','uuid_transaccion'];

  public function __construct(array $attributes = array()){
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_transaccion' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
  }

  function entrada_manual(){
    return $this->belongsTo('Entrada_orm','transaccionable_id')->where('transaccionable_type','Entrada_orm');
  }

  public function getUuidTransaccionAttribute($value){
		return strtoupper(bin2hex($value));
	}

  public static function findByUuid($uuid){
    return self::where('uuid_transaccion',hex2bin($uuid))->first();
  }

	public function transaccionable()
  {
     return $this->morphedByMany('Entrada_orm','transaccion','contab_transacciones','id');
  }


    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){


        $cuenta_arr=array();
        $condicion = array(
            'empresa_id' => $clause['empresa_id'],
            'padre_id' => isset($clause['cuenta_id']) ? $clause['cuenta_id'] : ''

        );

        $cuentas = Cuentas_orm::misCuentas($condicion,1);

        foreach($cuentas as $cuenta){

            $clause["cuenta_id"]=$cuenta["id"];

            $Trans = self::where(function($query) use($clause){
                $query->where('contab_transacciones.empresa_id','=',$clause['empresa_id']);
                if(isset($clause[0]['nombre']))$query->where('contab_transacciones.nombre','like' ,"%".$clause[0]['nombre']."%");
                if(isset($clause[0]['fecha1'])&&$clause[0]['fecha1']!="")$query->whereRaw('DATE_FORMAT(contab_transacciones.created_at,"%d-%m-%Y") >= "'.$clause[0]['fecha1'].'"');
                if(isset($clause[0]['fecha2'])&&$clause[0]['fecha2']!="")$query->whereRaw('DATE_FORMAT(contab_transacciones.created_at,"%d-%m-%Y") <= "'.$clause[0]['fecha2'].'"');
                if(isset($clause["cuenta_id"]))$query->where('contab_transacciones.cuenta_id','=',$clause["cuenta_id"]);
                if(isset($clause["cuentas_ids"]))$query->whereIn('contab_transacciones.cuenta_id',$clause["cuentas_ids"]);
            });

            $Trans->leftJoin('contab_entrada_manual', 'contab_transacciones.entrada_manual_id', '=', 'contab_entrada_manual.id');
            $Trans->leftJoin('faccom_facturas', 'contab_transacciones.nombre', '=', 'faccom_facturas.codigo');
            $Trans->leftJoin('sys_transacciones', 'sys_transacciones.id', '=', 'contab_transacciones.transaccionable_id');
//
            $Trans->select(
                'contab_transacciones.id',
                'contab_transacciones.nombre',
                //'contab_entrada_manual.codigo',
                'contab_transacciones.codigo',
                'contab_transacciones.debito',
                'contab_transacciones.created_at',
                'contab_transacciones.credito',
                'contab_entrada_manual.uuid_entrada',
                'faccom_facturas.uuid_factura',
                'sys_transacciones.id as id_trasaccion',
                'sys_transacciones.linkable_id',
                'sys_transacciones.linkable_type',
                'contab_transacciones.transaccionable_id',
                'contab_transacciones.transaccionable_type'

            );

            if($sidx!=NULL && $sord!=NULL)$Trans->orderBy("contab_transacciones.".$sidx,$sord);
            if($limit!=NULL) $Trans->skip($start)->take($limit);

            $resultado=$Trans->get()->toArray();

            if(count($resultado)>0){
                foreach($resultado as $cuent){
                array_push($cuenta_arr,$cuent);
                }
            }
        }

        return $cuenta_arr;

    }

}
