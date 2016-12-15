<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Items_retiros_orm extends Model
{
    
    protected $table = 'mov_retiros_items';
    protected $fillable = ['nombre', 'cuenta_id', 'centro_id', 'updated_at', 'created_at', 'debito', 'id_retiro'];
    protected $guarded = ['id'];
    
    
public static function listar($id_retiro, $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL){
   $query = self::where('id_retiro', array($id_retiro))->with(array('cuentas', 'centros'));
   
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