<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Contratos\Models\Contrato as Contrato;
use Flexio\Modulo\SubContratos\Models\SubContrato as SubContrato;

class Centros_orm extends Model
{
    protected $table = 'cen_centros';

    protected $fillable = ['nombre','empresa_id','padre_id','descripcion','estado','uuid_centro'];

    protected $guarded = ['id'];

    private  static $centros_contable = array();
    protected static $contieneTransacciones = [];

    public function toArray()
    {
      $array = parent::toArray();
      $array['hijos'] = $this->where('padre_id',$this->id)->count() == 0? true : false;
      return $array;
  }
    //padre
    public function centros(){
       return $this->belongsTo('Centros_orm', 'padre_id');
    }
    //hijos
    public function centros_item(){
      return $this->hasMany('Centros_orm','padre_id','id');
    }

    public function cuentas_contables(){
      return $this->belongsToMany('Cuentas_orm','contab_cuentas_centros','centro_id','cuenta_id')->withpivot('empresa_id');
    }

    public function departamentos(){
    	return $this->belongsToMany('Departamentos_orm','dep_departamentos_centros', 'centro_id', 'departamento_id');
    }

    public function getUuidCentroAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function empresa(){
      return $this->belongsTo('Empresa_orm');
    }

    public static function findByUuid($uuid){
      return self::where('uuid_centro',hex2bin($uuid))->first();
    }

    public function transacciones(){
      return $this->hasMany('Transaccion_orm','centro_id');
    }

    public function tieneTransaccion(){
      if($this->transacciones()->count() > 0){
        return true;
      }
      return false;
    }

    function scopeActivos($query)
    {
        return $query->where("estado", "Activo");
    }

    function scopeTransaccionalesDeEmpresa($query, $empresa_id)
    {
        return  $query->where("empresa_id", $empresa_id)
                ->whereNotIn("id", function($q) use ($empresa_id){
                    $q->select("padre_id")
                    ->from("cen_centros")
                    ->where("empresa_id", $empresa_id);
                });
    }

     static function listar($clause = array()){
      self::$centros_contable = array();
      $empresa_id = $clause['empresa_id'];
      $nombre="";
      $descripcion="";
      $estado ="";
      if(isset($clause['nombre'])){
        $nombre = $clause['nombre'];
        unset($clause['nombre']);
      }
      if(isset($clause['descripcion'])){
        $descripcion = $clause['descripcion'];
        unset($clause['descripcion']);
      }

      if(isset($clause['estado'])){
        $estado = $clause['estado'];
        unset($clause['estado']);
      }

    	if(empty($nombre) && empty($estado))$clause['padre_id'] = 0;
      //if(empty($estado))$clause['padre_id'] = 0;

      $result_search = Centros_orm::where(function($query) use ($clause,$nombre,$descripcion,$estado){
    		$query->where($clause);
        if(!empty($nombre))$query->where('nombre','like' ,"%$nombre%");
        if(!empty($descripcion))$query->where('descripcion','like' ,"%$descripcion%");
        if(!empty($estado))$query->where('estado',$estado);
    	});

      $padres = $result_search->get();
    	$padres->map(function($centros) use($empresa_id,$estado){
    		self::recursiva($centros, $empresa_id,$estado);
    	});
    	return self::$centros_contable;
    }

    static function  recursiva(Centros_orm $centro,$empresa_id, $estado=""){
    	$centros  = $centro->where(array('empresa_id' =>$empresa_id,'estado'=>$estado))->get();
      //foreach($centros as $centro){
        array_push(self::$centros_contable, $centro->toArray());
        if($centro->centros_item->count() > 0){

          $centro->centros_item->map(function($item) use($empresa_id,$estado){
            self::recursiva($item,$empresa_id,$estado);
          });

        }
  //  }
    }

    function cambiarEstado($centro, $estado){

      self::$contieneTransacciones = [];
      $this->recursivaTiene($centro, $estado);
      if(in_array(true, self::$contieneTransacciones)){
        return $response= array('estado'=>500,'mensaje' => '<b>¡Error!</b> El centro tiene transacciones asociadas');
      }
      $this->centroEstado($centro, $estado);
       return $response= array('estado'=>200,'mensaje' => '<b>¡&Eacute;xito!</b> La actualizaci&oacute;n de estado');
    }

    static function recursivaTiene(Centros_orm $centro, $estado)
    {

      if($centro->tieneTransaccion() && $estado=='Inactivo'){
         array_push(self::$contieneTransacciones,true);
      }
      if($centro->centros_item->count() > 0){
        $centro->centros_item->map(function($item) use($estado){
             self::recursivaTiene($item, $estado);
          });
      }
       return self::$contieneTransacciones;
    }

    function centroEstado($centro, $estado){
      if($estado =="Activo"){
        self::updatePadreEstado($centro, $estado);
      }
      self::updateHijoEstado($centro, $estado);
    }

  static  function updatePadreEstado($centro, $estado){
      $centro->estado = $estado;
      $centro->save();
      $centro = Centros_orm::where('id',$centro->padre_id)->get()->last();
      if(!is_null($centro)){
         self::updatePadreEstado($centro, $estado);
      }
    }

  static  function updateHijoEstado($centro, $estado){
    $centro->estado = $estado;
    $centro->save();

      if($centro->centros_item->count() > 0){
        $centro->centros_item->map(function($item) use($estado){
             self::updateHijoEstado($item, $estado);
          });
      }
    }

    function contrato(){
      return $this->hasMany(Contrato::class,'centro_id');
    }

    function centrosConContratos($clause = []){
      return self::whereHas('contrato',function($query) use($clause){
        $query->where('empresa_id','=',$clause['empresa_id']);
      })->get();
    }

  /**
   * -----------------------------------------------------
   * Centros con Subcontratos
   * -----------------------------------------------------
   */
  public function subcontrato()
  {
    return $this->hasMany(SubContrato::class, 'centro_id');
  }

  public function centrosConSubcontratos($clause = [])
  {
    return self::whereHas('subcontrato', function($query) use ($clause){
      $query->where('empresa_id','=',$clause['empresa_id']);
    })->get();
  }

}
