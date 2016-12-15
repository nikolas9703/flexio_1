<?php
namespace Flexio\Modulo\Usuarios\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class RolesUsuario extends Model
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
      return $this->belongsTo('Flexio\Modulo\Roles\Models\Roles', 'role_id', 'id');
  }

  public function usuarios()
  {
      return $this->belongsTo(Usuarios::class, 'usuario_id', 'id');
  }

}
