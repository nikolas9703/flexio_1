<?php

namespace Flexio\Modulo\Cliente\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class ClienteCatalogo extends Model
{
    protected $table = 'cli_clientes_catalogo';
}
