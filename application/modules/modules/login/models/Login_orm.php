<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//namespace Erp;
use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
//use \Modules\Roles\Models as Roles;
class Login_orm extends Model{

  protected $table = 'usuarios';
  protected $fillable = ['nombre','apellido', 'email','last_login','usuario','fecha_creacion','last_login_ip_address','ip_address','uuid_usuario','recovery_token','password','estado'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $hidden = array('password','recovery_token');


/**
     * Instancia de CodeIgniter
     */
    protected $Ci;


    public function __construct() {
        $this->Ci = & get_instance();
    }

  //sobreescribe el retorno del resultado
  public function toArray()
  {
    $array = parent::toArray();
    $array['nombre_completo'] =  $this->attributes['nombre'] ." ".$this->attributes['apellido'];
    return $array;
  }

  public function setUuidUsuarioAttribute($value)
  {
    $this->attributes['uuid_usuario'] = Capsule::raw("ORDER_UUID(uuid())");
  }

  public function getUuidUsuarioAttribute($value)
  {
    return strtoupper(bin2hex($value));
  }

  public function empresas(){
    return $this->belongsToMany('Empresa_orm','usuarios_has_empresas','usuario_id','empresa_id');
  }


  public function setEmailAttribute($value){
    $this->attributes['email']= strtolower($value);
  }

    public function conversion2bin($value){
      return hex2bin($value);
    }

      //retorn el usuario
    public static function validar_token($clause){
      return Login_orm::where(function($query) use ($clause){
        $query->where('recovery_token',$clause['recovery_token']);
        $query->where('estado',$clause['estado']);
      })->orWhere(function($query) use ($clause){
        $query->where('estado','Activo');
        $query->where('recovery_token',$clause['recovery_token']);
      })->first();
    }

    public function roles(){
        $this->Ci->load->model("roles/Rol_orm");
      return $this->belongsToMany('Rol_orm','usuarios_has_roles','usuario_id','role_id','empresa_id');
    }

    public static function check_username($clause){
      return Login_orm::where($clause)->first();
    }

}
