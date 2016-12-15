<?php
namespace Flexio\Modulo\Modulos\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class Modulos  extends Model{
	protected $table = 'modulos';
	protected $fillable = ['nombre', 'descripcion','icono','controlador','version','tipo', 'grupo', 'agrupador','menu','agrupador_orden'];
	protected $guarded = ['id'];
    protected $casts =['agrupador' => 'array'];
	public $timestamps = false;
    
 
}
