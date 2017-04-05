<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Rol_orm extends Model
{
  protected $table = 'roles';
  protected $fillable = ['empresa_id', 'nombre', 'descripcion', 'superuser', 'default'];
  protected $guarded = ['id'];

  public function usuarios(){
    return $this->belongsToMany('Usuario_orm','usuarios_has_roles','role_id','usuario_id')->withPivot('empresa_id');
  }

  public function empresas(){
	return $this->belongsToMany('Empresa_orm', 'empresas_has_roles', 'rol_id', 'empresa_id');
  }

  public function permisos(){
  	return $this->belongsToMany('Permiso_orm', 'roles_permisos', 'rol_id', 'permiso_id');
  }

  /**
   * Conteo de los roles existentes
   *
   * @return [array] [description]
   */
  public static function listar($clause=array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
  {
  	$query = self::whereNotIn('id', array(1,2,3));

  	//Si existen variables de limite
  	if($clause!=NULL && !empty($clause) && is_array($clause))
  	{
  		foreach($clause AS $field => $value)
  		{
  			if(is_array($value)){
  				$query->where($field, $value[0], $value[1]);
  			}else{
  				$query->where($field, '=', $value);
  			}
  		}
  	}

  	//Si existen variables de orden
  	if($sidx!=NULL && $sord!=NULL) $query->orderBy($sidx, $sord);

  	//Si existen variables de limite
  	if($limit!=NULL) $query->skip($start)->take($limit);

  	return $query->get(array('id', 'empresa_id', 'nombre', 'descripcion', 'superuser', 'default', Capsule::raw("IF(estado=1, 'Activo', 'Inactivo') AS estado"), Capsule::raw("IF(superuser=1, 'Si', 'No') AS superuserValue")));
  }
}
