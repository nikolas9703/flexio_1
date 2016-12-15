<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Ramos_orm extends Model
{
	protected $table = 'seg_ramos';
	protected $fillable = ['codigo','nombre','descripcion','codigo_ramo','id_tipo_int_asegurado','id_tipo_poliza','agrupador','empresa_id','padre_id'];
	protected $guarded = ['id'];
  private  static $ramos_ = array();
	private static $total_estado = 0;
        protected $appends = ['is_padre'];
	public function __construct(array $attributes = array()) {
    $this->setRawAttributes(array_merge($this->attributes, array(
      'uuid_ramos' => Capsule::raw("ORDER_UUID(uuid())")
    )), true);
    parent::__construct($attributes);
}


	public function getUuidCuentaAttribute($value) {
		return strtoupper(bin2hex($value));
	}
        
    public function getIsPadreAttribute() {
			return $this->ramos_item->count() > 0;
	}
    
    public static function findByUuid($uuid) {
        return self::where('uuid_ramos',hex2bin($uuid))->first();
    }

  public function ramos() {
     return $this->belongsTo('Ramos_orm', 'padre_id');
  }

  public function ramos_item() {
    return $this->hasMany('Ramos_orm','padre_id','id')->orderBy('nombre','ASC');
  }

  public function empresa() {
    return $this->belongsTo('Empresa_orm');
  }
  public function interesAsegurado() {
    return $this->belongsTo('Catalogo_tipo_intereses_orm',"id_tipo_int_asegurado");
  }
  public function tipoPoliza() {
    return $this->belongsTo('Catalogo_tipo_poliza_orm',"id_tipo_poliza");
  }
  

	public function impuesto() {
		return $this->belongsTo('Impuesto_orm');
	}


public static function listar($clause = array(), $nombre, $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL) {

      /*/$padres = Ramos_orm::where('padre_id', 0)->where($clause)->toSql();
      echo $padres;*/
      self::$ramos_ = array();
			$empresa_id = $clause['empresa_id'];
      if(empty($nombre))$clause['padre_id'] = 0;

      $result_search = Ramos_orm::where(function($query) use ($clause, $nombre){
        $query->where($clause);
        if(isset($nombre))$query->where('nombre','like' ,"%$nombre%")->orderBy('nombre','ASC');
      })->orderBy('nombre','ASC');

      
      $padres = $result_search->get();
      
      
      $padres->map(function($ramos) use($empresa_id){
        $i = 0; //level 0 padres
        self::recursiva($ramos, $empresa_id,$i);
      });
      return self::$ramos_;




}

static function listar_cuentas($clause = array()) {
	self::$ramos_ = array();
	$empresa_id = $clause['empresa_id'];
	$clause['padre_id'] = 0;

	$result_search = Ramos_orm::where(function($query) use ($clause){
		$query->where($clause)->orderBy('nombre','ASC');
	})->orderBy('nombre','ASC');

	$padres = $result_search->get();
	$padres->map(function($ramos) use($empresa_id){
            $i = 0; //level 0 padres
		self::recursiva($ramos, $empresa_id,$i);
	});
	return self::$ramos_;
}

static function  recursiva(Ramos_orm $cuenta,$empresa_id, $level) {
		$cuenta->where('empresa_id', $empresa_id)->orderBy('nombre','ASC')->get();
                $cuenta->interesAsegurado;
                $cuenta->tipoPoliza;
                $level++;
                $AUX = $cuenta->toArray();
                $AUX["level"] = $level;
                array_push(self::$ramos_, $AUX);
                if($cuenta->ramos_item->where('empresa_id', $empresa_id)->count() > 0){

                  $cuenta->ramos_item->where('empresa_id', $empresa_id)->map(function($item) use($empresa_id,$level){
                      
                    self::recursiva($item,$empresa_id,$level);
                  });

                }
}

//retorna si es padre,
public static function is_parent($id) {
  //if()

  $padre = self::find($id);
  if($padre->padre_id==0)return true;

   $total = self::where('padre_id',$id)->count();

   if($total > 0){
     return true;
   }else{
     return false;
   }
}


public static function codigo($codigo) {
	$code_array = explode('.',$codigo);
	$count = count($code_array) - 2;
	$selecionado = $code_array[$count];
	$digito = strlen($selecionado);
	$nuevo = sprintf("%0$digito".'d',$selecionado + 1);
	$code_array[$count] = $nuevo;
	$new_code = implode(".", $code_array);
	return $new_code;
}

public static function cambiar_estado($id, $estado) {
  $ramos = self::find($id);
	self::$total_estado = 0;
	self::recursiva_cambiar_estado($ramos, $estado);
  return self::$total_estado;
}

static function recursiva_cambiar_estado(Ramos_orm $cuenta, $estado) {
		//$cuenta->where('empresa_id', $empresa_id)->get();
		$cuenta->estado = $estado;
		if($cuenta->save()){
			self::$total_estado++;
		}

    if($cuenta->ramos_item->count() > 0){
      $cuenta->ramos_item->map(function($item) use($estado){
        self::recursiva_cambiar_estado($item,$estado);
      });

    }
}

    //lista solo las cuentas activas
    function scopeActivas($query) {
        return $query->where("estado", "1");
    }
    
    //lista solo las cuentas transaccionales
    function scopeTransaccionalesDeEmpresa($query, $empresa_id) {
        return  $query->where("empresa_id", $empresa_id)
                ->whereNotIn("id", function($q) use ($empresa_id){
                    $q->select("padre_id")
                    ->from("contab_cuentas")
                    ->where("empresa_id", $empresa_id);
                });
    }

    function scopeDeTipoDeCuenta($query, $tipos) {
        return  $query->whereIn("tipo_cuenta_id", $tipos);
    }
    
    public static function findCodigo($clause) {
        return self::where($clause)->first();
    }
}
