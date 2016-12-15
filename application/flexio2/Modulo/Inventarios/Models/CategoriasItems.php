<?php
namespace Flexio\Modulo\Inventarios\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class CategoriasItems extends Model
{
    protected $table    = 'inv_items_categorias';
    public $timestamps  = false;
    protected $fillable = ['id_item','id_categoria'];
    protected $guarded  = ['id'];
}
