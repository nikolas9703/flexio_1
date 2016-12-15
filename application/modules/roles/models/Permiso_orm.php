<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Permiso_orm extends Model
{
  protected $table = 'permisos';
  protected $fillable = ['nombre', 'recurso_id'];
  protected $guarded = ['id'];
  
  /**
     * Instancia de CodeIgniter
     */
    protected $Ci;
    
    
    public function __construct() {
        $this->Ci = & get_instance();
    }
  
  public function roles(){
        $this->Ci->load->model("roles/Rol_orm");
  	return $this->belongsToMany('Rol_orm','roles_permisos','permiso_id','rol_id');
  }
}
