<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

use Flexio\Modulo\aseguradoras\Models\Aseguradoras;
 
class Movimiento_monetario_orm extends Model
{
    
    protected $table = 'mov_recibo_dinero';
    protected $fillable = [ 'codigo', 'narracion', 'created_at', 'updated_at', 'fecha_inicio', 'empresa_id', 'cliente_id', 'proveedor_id', 'aseguradora_id','estado', 'cuenta_id', 'incluir_narracion'];
    protected $guarded = ['id'];
    
    public function __construct(array $attributes = array()){
    	$this->setRawAttributes(array_merge($this->attributes, array('uuid_recibo_dinero' => Capsule::raw("ORDER_UUID(uuid())"))), true);
    	parent::__construct($attributes);
    }
    
    public function getUuidReciboDineroAttribute($value)
    {
    	return strtoupper(bin2hex($value));
    }
    
    
public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
	$query = self::with(array('cliente', 'proveedor', 'aseguradora','items' => function($query){
            
    		}));
                
    if($clause!=NULL && !empty($clause) && is_array($clause))
    {
        $query->where("estado", "1")->where("empresa_id", $clause['id_empresa']); 
            
           if(!empty($clause['cliente'])){
               
             if($clause['cliente']=="1"){
              
                $query->whereHas('proveedor',function($query) use($clause){
                $query->where('id','=',$clause['nombre']);
                })->get();
            
                 
             }elseif($clause['cliente']=="2"){
                 
              $query->whereHas('cliente',function($query) use($clause){
              $query->where('id','=',$clause['nombre']);
              })->get();     
                  
                   
              }else{
               $query->whereHas('aseguradora',function($query) use($clause){
                  $query->where('id','=',$clause['nombre']);
                })->get();     
              }

          }

        
           if(!empty($clause['narracion']))
            {
            $valor_narracion = $clause['narracion'][1];
            $query->where("narracion", "LIKE", "%$valor_narracion%");
            }  
            
            if(!empty($clause['monto_desde']) || !empty($clause['monto_hasta'])){
              $query->join('mov_recibos_items','mov_recibos_items.id_recibo','=','mov_recibo_dinero.id');
            }
           if(!empty($clause['monto_desde'])){
            $query->where("credito", ">=", $clause['monto_desde']); 
           }   
           if(!empty($clause['monto_hasta'])){
            $query->where("credito", "<=", $clause['monto_hasta']); 
           } 
           if(!empty($clause['fecha_desde']))
           {
            $fecha_desde = $clause['fecha_desde'];
            $query->where("fecha_inicio", ">=", $fecha_desde);                 
           }
           if(!empty($clause['fecha_hasta']))
           {
            $fecha_hasta = $clause['fecha_hasta'];
            $query->where("fecha_inicio", "<=", $fecha_hasta);                 
           }
            
    }      
    
	if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);
	if($limit!=NULL) $query->skip($start)->take($limit);
        
  return $query->select('mov_recibo_dinero.*')->get();
  }
  
public function cliente()
{
    return $this->hasOne('Cliente_orm','id','cliente_id');    
}

public function proveedor()
{
    
    return $this->hasOne('Proveedores_orm', 'id', 'proveedor_id');
    
}

public function aseguradora(){
    return $this->hasOne(Aseguradoras::class, 'id', 'aseguradora_id');
}

public static function findByUuid($uuid){
    return self::where('uuid_recibo_dinero',hex2bin($uuid))->first();
  }

public function items()
	{
		return $this->hasMany('Items_recibos_orm', 'id_recibo');
	}
    
public function transacciones()
	{
		return $this->hasMany('Items_recibos_orm', 'id_recibo');
	}       

public function cuentas()
{
    return $this->hasMany('Cuentas_orm', 'id');    
}     

public function getNumeroDocumentoAttribute()
{

	return $this->codigo;
}

public function getNumeroDocumentoEnlaceAttribute()
{
	$attrs = [
	"href"  => $this->enlace,
	"class" => "link"
			];
 
	$html = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory);
	return $html->setType("HtmlA")->setAttrs($attrs)->setHtml($this->numero_documento)->getSalida();
}
public function getEnlaceAttribute()
{
	return base_url("movimiento_monetario/ver/".$this->uuid_recibo_dinero);
}

}