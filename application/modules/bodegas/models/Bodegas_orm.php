<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Bodegas_orm extends Model
{
    /**
     * Esta es la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'bod_bodegas';
    
    
    /**
     * Indica si el modelo usa timestamp
     * created_at este campo debe existir en el modelo
     * updated_at este campo debe existir en el modelo
     *
     * @var bool
     */
    public $timestamps = false;
    
    
    /**
     * Indica el formato de la fecha en el modelo
     * en caso de que aplique
     *
     * @var string
     */
    protected $dateFormat = 'U';
    
    
    /**
     * Estos atributos son asignables
     *
     * @var array
     */
    protected $fillable = ['uuid_bodega, codigo, nombre, contacto_principal, direccion, telefono, entrada_id, estado, empresa_id'];
    
    
    /**
     * Estos atributos no son asignables
     *
     * @var array
     */
    protected $guarded      = ['id'];
    private static $bodegas = array();
    
    
    public static function findByUuid($uuid){
        return self::where('uuid_bodega',hex2bin($uuid))->first();
    }
    
    public function  uuid()
    {
        return $this->uuid_bodega;
    }


    
    /**
     * Obtiene uuid_centro
     * 
     * Se convierte la data binaria en una representacion
     * hexadecimal
     * 
     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    public function getUuidBodegaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    
    public function nombreCompleto()
    {
        return $this->codigo." - ".$this->nombre;
    }
    
    
    public function entrada()
    {
        return $this->belongsTo('Bodegas_cat_orm', 'entrada_id', 'id_cat');
    }
    
    public function ajustes()
    {
        return $this->hasMany('Ajustes_orm', 'uuid_bodega', 'uuid_bodega');
    }
    
    public function ordenes_compras()
    {
        return $this->hasMany('Ordenes_orm', 'uuid_lugar', 'uuid_bodega');
    }
    
    public function traslados()
    {
        return $this->hasMany('Traslados_orm', 'uuid_lugar', 'uuid_bodega');
    }
       
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where('empresa_id', $empresa_id);
    }
    
    public function scopeDeNombre($query, $nombre)
    {
        return $query->where('nombre', 'like', "%$nombre%");
    }
    
    public function scopeDeCodigo($query, $codigo)
    {
        return $query->where('codigo', 'like', "%$codigo%");
    }
    
    public function scopeDeContactoPrincipal($query, $contacto_principal)
    {
        return $query->where('contacto_principal', 'like', "%$contacto_principal%");
    }
    
    public function scopeDeDireccion($query, $direccion)
    {
        return $query->where('direccion', 'like', "%$direccion%");
    }
    
    public function scopeDeTelefono($query, $telefono)
    {
        return $query->where('telefono', 'like', "%$telefono%");
    }
    
    public function scopeActivas($query)
    {
        return $query->where('estado', '1');
    }
    
    public function scopeSinEntradas($query)
    {
        return $query->where(function($q){
            $q->has("ajustes", "=", "0");
            $q->has("traslados", "=", "0");
            $q->has("ordenes_compras", "=", "0");
        });
    }
    
    public function scopeTransaccionales($query, $empresa_id)
    {
        $ids = Bodegas_orm::deEmpresa($empresa_id)->lists('padre_id');
        
        return $query->whereNotIn("id", $ids);
    }
    
    public function scopePadres($query)
    {
        return $query->where("padre_id", "0");
    }
    
    public function toArray()
    {
      $array = parent::toArray();
      $array['hijos']       = $this->where('padre_id',$this->id)->count() == 0? true : false;
      $array['entradas']    = $this->where(function($q){
                                $q->where("id", $this->id);
                                $q->has("ajustes", "=", "0");
                                $q->has("traslados", "=", "0");
                                $q->has("ordenes_compras", "=", "0");
                            })->count() == 0 ? true : false;
      return $array;
    }
    
    
    //OBTENER BODEGAS DE FORMA JERARQUICA
    public static function listar($padres){
        self::$bodegas = array();
        
        $padres->map(function($bodega){
            self::recursiva($bodega);
    	});
        
    	return self::$bodegas;
    }
    
    public function getRaizAttribute()
    {
        return $this->recursiveGetRaiz(Bodegas_orm::find($this->id));
    }
    
    private function recursiveGetRaiz($bodega)
    {
        if(count($bodega->padre) > 0)
        {
            return $this->recursiveGetRaiz(Bodegas_orm::find($bodega->padre_id));
        }
        return $bodega;
    }
    
    public function padre()
    {
        return $this->belongsTo('Bodegas_orm', "padre_id", "id");
    }

    private static function recursiva(Bodegas_orm $bodega){
        
        array_push(self::$bodegas, $bodega->toArray());
        if($bodega->bodega_hijos->count() > 0){
            $bodega->bodega_hijos->map(function($hijo){
                self::recursiva($hijo);
            });
        }
    }
    
    public function bodega_hijos(){
        return $this->hasMany('Bodegas_orm','padre_id','id');
    }
    
    	
}