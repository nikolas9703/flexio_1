<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Items_recibos_orm extends Model
{
    
    protected $table = 'mov_recibos_items';
    protected $fillable = ['nombre', 'cuenta_id', 'centro_id', 'updated_at', 'created_at', 'credito', 'id_recibo', 'recibo_item_id'];
    protected $guarded = ['id'];
    
    
public static function listar($id_recibo, $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
   $query = self::where('id_recibo', array($id_recibo))->with(array('cuentas', 'centros'));
   
   return $query->get()->toArray();
    
  }
  
public function cliente()
{
    return $this->hasOne('Cliente_orm','id','cliente_id');    
}   

public function proveedor()
{
    
    return $this->hasOne('Proveedores_orm', 'id', 'proveedor_id');
    
}

public function cuentas()
{
    return $this->hasOne('Cuentas_orm','id', 'cuenta_id');    
}

public function centros()
{
    return $this->hasOne('Centros_orm','id', 'centro_id');    
}
    

}