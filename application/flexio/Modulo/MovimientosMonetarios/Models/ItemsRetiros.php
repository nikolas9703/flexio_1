<?php
namespace Flexio\Modulo\MovimientosMonetarios\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class ItemsRetiros extends Model
{
    protected $table = 'mov_retiros_items';
    protected $fillable = ['nombre', 'cuenta_id', 'centro_id', 'updated_at', 'created_at', 'debito', 'id_retiro'];
    protected $guarded = ['id'];
}
