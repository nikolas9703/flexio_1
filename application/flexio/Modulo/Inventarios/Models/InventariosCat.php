<?php
namespace Flexio\Modulo\Inventarios\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class InventariosCat extends Model
{
    protected $table    = 'inv_inventarios_cat';
    public $timestamps  = false;
    protected $fillable = ['etiqueta'];
    protected $guarded  = ['id_cat'];
}
