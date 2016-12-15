<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class Roles_usuarios_orm extends Model
{
  protected $table = 'usuarios_has_roles';

  /**
   * Indica si el modelo usa timestamp
   * created_at este campo debe existir en el modelo
   * updated_at este campo debe existir en el modelo
   *
   * @var bool
   */
  public $timestamps = false;

  protected $fillable = ['usuario_id', 'role_id', 'empresa_id'];

  protected $guarded = ['id'];


  public function roles()
  {
      return $this->belongsTo('Rol_orm', 'role_id', 'id');
  }

  public function usuarios()
  {
      return $this->belongsTo('Usuario_orm', 'usuario_id', 'id');
  }

}
