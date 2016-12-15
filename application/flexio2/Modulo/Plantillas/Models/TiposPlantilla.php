<?php
namespace Flexio\Modulo\Plantillas\Models;
use \Illuminate\Database\Eloquent\Model as Model;

class TiposPlantilla extends Model
{
    protected $table	= 'plnt_tipos';
    protected $fillable	= ['nombre', 'estado', 'orden'];
    protected $guarded	= ['id'];
}
