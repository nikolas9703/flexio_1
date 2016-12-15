<?php
namespace Flexio\Modulo\Talleres\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class EquipoCentrosContables extends Model
{
    protected $table    = 'tal_centros';
   	protected $fillable = ['equipo_id', 'centro_padre_id', 'centro_id', 'departamento_id'];
    protected $guarded	= ['id'];
    public $timestamps      = true;
}
