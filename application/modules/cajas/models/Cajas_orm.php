<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Cajas_orm extends Model
{
    protected $table = 'ca_cajas';
    public $timestamps = false;
    protected $dateFormat = 'U';
  	protected $fillable = ['uuid_caja', 'nombre', 'limite', 'balance','uuid_centro', 'responsable','numero','uuid_empresa'];
    protected $guarded = ['id'];
    
    
    /**
     * Instancia de CodeIgniter
     */
    protected $Ci;
    
    
    public function __construct() {
        $this->Ci = & get_instance();
    }
    
    /**
     * Obtiene uuid_pedido
     * 
     * Se convierte la data binaria en una representacion
     * hexadecimal
     * 
     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    
    /*public function toArray(){
        $array = parent::toArray();
        $array['saldo_pendiente'] = number_format($this->total_saldo_pendiente(), 2, '.', ',');
        return $array;
    }*/
    
    public function getUuidCajaAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where('id_empresa', $empresa_id);
    }
    
    public function scopeConFacturasParaPagos($query)
    {
        return  $query->whereHas('facturas', function($q){
                $q->where(function($q2){
                    $q2->where("estado_id", "14")//por facturar
                    ->orWhere("estado_id", "15");//facturado paracial
                });
        });
    }
    /**
     * Obtiene fecha de creacion formateada
     
     * Para el ERP se transforma en mayuscula
     *
     * @param  string  $value
     * @return string
     */
    public function getFechaCreacionAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }
    
    /**
     * Obtiene el registro de centro asociado con el pedido.
     */
    public function centro()
    {
       $this->Ci->load->model("centros/Centros_orm");
       return $this->belongsTo('Centros_orm', 'uuid_centro', 'uuid_centro');
    }
    
    /**
     * Conteo/Listado de cajas
     *
     * @return
     */
    public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
    	$query = self::with(array("centro"));

    	//Si existen variables de limite
    	if($clause!=NULL && !empty($clause) && is_array($clause))
    	{
    		foreach($clause AS $field => $value)
    		{
    			if($field == "cargo" || $field == "departamento" || $field == "departamento_id" ||   $field == "colaborador"  || $field == "id" || $field == "nombre_centro"){
    				continue;
    			}

    			//Verificar si el campo tiene el simbolo @ y removerselo.
    			if(preg_match('/@/i', $field)){
    				$field = str_replace("@", "", $field);
    			}
    
    			//verificar si valor es array
    			if(is_array($value)){
    				$query->where($field, $value[0], $value[1]);
    			}else{
    				$query->where($field, '=', $value);
    			}
    		}
    	}
    
    	//Si existen variables de orden
    	if($sidx!=NULL && $sord!=NULL){
    		if(!preg_match("/(cargo|departamento|centro_contable)/i", $sidx)){
    			$query->orderBy($sidx, $sord);
    		}
    	}
    
    	//Si existen variables de limite
    	if($limit!=NULL) $query->skip($start)->take($limit);
    	return $query->get();
    }
}
   