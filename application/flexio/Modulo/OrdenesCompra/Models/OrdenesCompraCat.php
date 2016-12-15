<?php namespace Flexio\Modulo\OrdenesCompra\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class OrdenesCompraCat extends Model
{
    public $timestamps  = false;
    
    protected $table    = 'ord_ordenes_cat';
    protected $fillable = ['etiqueta'];
    protected $guarded  = ['id_cat'];
    	
}