<?php
namespace Flexio\Modulo\Usuarios\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Ramos\Models\RamosRoles;
use Flexio\Modulo\CentrosContables\Models\CentrosContables;

class CentrosUsuario extends Model
{
  protected $table = 'usuarios_has_centros';

  /**
   * Indica si el modelo usa timestamp
   * created_at este campo debe existir en el modelo
   * updated_at este campo debe existir en el modelo
   *
   * @var bool
   */
  public $timestamps = false;

  protected $fillable = ['usuario_id', 'centro_id', 'empresa_id'];

  protected $guarded = ['id'];


}
