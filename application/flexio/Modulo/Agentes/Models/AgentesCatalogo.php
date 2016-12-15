<?php
namespace Flexio\Modulo\Agentes\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class AgentesCatalogo extends Model
{
    protected $table = 'agt_agentes_catalogos';
    protected $fillable = ['key','identificador','etiqueta', 'valor', 'orden'];
    protected $guarded = ['id_cat'];
    public $timestamps = false;

    protected $primaryKey = 'id_cat';
}