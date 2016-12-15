<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 6/10/16
 * Time: 1:22 PM
 */

namespace Flexio\Modulo\ConfiguracionVentas\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class CatalogosClientes extends Model
{
    protected $table = 'cli_clientes_catalogo';
    protected $guarded = ['id','uuid_cliente'];
}