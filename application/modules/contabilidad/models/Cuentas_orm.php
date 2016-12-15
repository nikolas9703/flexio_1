<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\ConfiguracionContabilidad\Models\CuentaPorCobrar as CuentaPorCobrar;
class Cuentas_orm extends Model
{
	protected $table = 'contab_cuentas';
	protected $fillable = ['codigo','nombre','detalle','padre_id','tipo_cuenta_id','empresa_id','impuesto_id','uuid_cuenta'];
	protected $guarded = ['id'];
  private  static $cuentas_contable = array();
	private static $total_estado = 0;
	protected $appends = ['is_padre'];

	public function __construct(array $attributes = array())
{
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_cuenta' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
}

	public function centros_contables(){
		return $this->belongsToMany('Centros_orm','contab_cuentas_centros','cuenta_id','centro_id')->withpivot('empresa_id');
	}


	public function getUuidCuentaAttribute($value){
		return strtoupper(bin2hex($value));
	}

  function tipo_cuentas(){
    return $this->belongsTo('Tipo_cuentas_orm','tipo_cuenta_id');
  }

    public static function findByUuid($uuid){
        return self::where('uuid_cuenta',hex2bin($uuid))->first();
    }

	public function getIsPadreAttribute(){
			return $this->cuentas_item->count() > 0;
	}

  /*public function cuentas_item(){
    return $this->belongsTo('Item_cuentas_orm','contab_cuentas','padre_id')->where('padre_id',0);
  }*/
  public function cuentas(){
     return $this->belongsTo('Cuentas_orm', 'padre_id');
  }

  public function cuentas_item(){
    return $this->hasMany('Cuentas_orm','padre_id','id');
  }

  public function empresa(){
    return $this->belongsTo('Empresa_orm');
  }

	public function config_cuenta_por_cobrar(){
		return $this->hasOne('CuentaPorCobrar','cuenta_id');
	}

	public function impuesto(){
		return $this->hasMany('Impuestos_orm','cuenta_id');
	}


  public static function listar($clause = array(), $nombre=null, $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){

    self::$cuentas_contable = array();
    $empresa_id = $clause['empresa_id'];

    $result_search = Cuentas_orm::where(function($query) use ($clause, $nombre){
                //$query->where($clause);
      if(!empty($nombre)){$query->where('nombre','like' ,"%$nombre%");}
      if(isset($clause["uuid_cuenta"]) and !empty($clause["uuid_cuenta"])){$query->where('uuid_cuenta', hex2bin($clause["uuid_cuenta"]));}
      if(isset($clause["empresa_id"]) and !empty($clause["empresa_id"])){$query->where('empresa_id',  $clause["empresa_id"]);}
      if(isset($clause["tipo_cuenta_id"])) $query->where('tipo_cuenta_id',$clause["tipo_cuenta_id"]);
    });

    $padres = $result_search->get();
    foreach($padres as $cuentas){
      self::recursiva($cuentas, $empresa_id);
    }

    return self::$cuentas_contable;
    }

static function listar_cuentas($clause = array()){
	self::$cuentas_contable = array();
	$empresa_id = $clause['empresa_id'];
	$clause['padre_id'] = 0;

	$result_search = Cuentas_orm::where(function($query) use ($clause){
		$query->where($clause);
	});

	$padres = $result_search->get();
	//dd($padres);
	$padres->map(function($cuentas) use($empresa_id){
		self::recursiva($cuentas, $empresa_id);
	});
	return self::$cuentas_contable;
}

static function  recursiva(Cuentas_orm $cuenta,$empresa_id){
		//$cuentas = $cuenta->where(array('empresa_id'=> $empresa_id))->get();
    array_push(self::$cuentas_contable, $cuenta->toArray());
    if($cuenta->cuentas_item->where('empresa_id', $empresa_id)->count() > 0){

      $cuenta->cuentas_item->where('empresa_id', $empresa_id)->map(function($item) use($empresa_id){
        self::recursiva($item,$empresa_id);
      });

    }
}

//retorna si es padre,
public static function is_parent($id){

  $padre = self::find($id);
  if($padre->padre_id==0)return 1;

   $total = self::where('padre_id',$id)->count();

   if($total > 0){
     return 1;
   }else{
     return 0;
   }
}

public static function tipo($id){

return self::find($id)->tipo_cuentas->nombre;

}

public static function codigo($codigo){
	$code_array = explode('.',$codigo);
	$count = count($code_array) - 2;
	$selecionado = $code_array[$count];
	$digito = strlen($selecionado);
	$nuevo = sprintf("%0$digito".'d',$selecionado + 1);
	$code_array[$count] = $nuevo;
	$new_code = implode(".", $code_array);
	return $new_code;
}

public static function cambiar_estado($id, $estado){
  $cuentas = self::find($id);
	self::$total_estado = 0;
	self::recursiva_cambiar_estado($cuentas, $estado);
  return self::$total_estado;
}

static function recursiva_cambiar_estado(Cuentas_orm $cuenta, $estado){
		//$cuenta->where('empresa_id', $empresa_id)->get();
		$cuenta->estado = $estado;
		if($cuenta->save()){
			self::$total_estado++;
		}

    if($cuenta->cuentas_item->count() > 0){
      $cuenta->cuentas_item->map(function($item) use($estado){
        self::recursiva_cambiar_estado($item,$estado);
      });

    }
}

    //lista solo las cuentas activas
    function scopeActivas($query)
    {
        return $query->where("estado", "1");
    }

    //lista solo las cuentas transaccionales
    function scopeTransaccionalesDeEmpresa($query, $empresa_id)
    {
        return  $query->where("empresa_id", $empresa_id)
                ->whereNotIn("id", function($q) use ($empresa_id){
                    $q->select("padre_id")
                    ->from("contab_cuentas")
                    ->where("empresa_id", $empresa_id);
                });
    }

    function scopeDeTipoDeCuenta($query, $tipos)
    {
        return  $query->whereIn("tipo_cuenta_id", $tipos);
    }

		static function cuentasBanco($clause){
			return self::where(function($query) use ($clause){
				$query->activas();
				$query->where('empresa_id','=',$clause['empresa_id']);
				$query->where('codigo','like','1.1.2.0%');
			})->get(['id','codigo','nombre','uuid_cuenta']);
		}

		public static function misCuentas($clause = [],$historial=0){
			self::$cuentas_contable = array();
			if(empty($clause['empresa_id']) && !isset($clause['empresa_id']))return self::$cuentas_contable;

			$empresa_id = $clause['empresa_id'];
            if(empty($clause['padre_id']) && !isset($clause['padre_id']))$clause['padre_id']=0;

			$result_search = Cuentas_orm::where(function($query) use ($clause,$historial){
                if($historial==1){
                    $query->where($clause)->orWhere('id', '=', $clause["padre_id"]);
                }
                else{
                    $query->where($clause);
                }

			});


			$padres = $result_search->get();

            $padres->map(function($cuentas) use($clause){
				self::cuentas_recursiva($cuentas, $clause,1);
			});
			return self::$cuentas_contable;
		}

		static function  cuentas_recursiva(Cuentas_orm $cuenta,$clause,$historial=0){
            if($historial==1){
                $clause["id"]=$clause["padre_id"];
            }
				$cuenta->where($clause)->get();
		    array_push(self::$cuentas_contable, $cuenta->toArray());
		    if($cuenta->cuentas_item->where('empresa_id',$clause['empresa_id'])->count() > 0){
			      $cuenta->cuentas_item->where('empresa_id',$clause['empresa_id'])->map(function($item) use($clause){
                      if($clause["id"]){
                          unset($clause["id"]);
                      }
			        self::cuentas_recursiva($item,$clause);
			      });
		    }
		}




}
