<?php
namespace Flexio\Modulo\Agentes\Models;

use \Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class AgentesRamos extends Model
{
    protected $table = 'agt_agentes_ramos';
    protected $fillable = ['id_agente','id_ramo', 'participacion', 'id_cliente', 'detalle_unico'];
    protected $guarded = ['id'];
    public $timestamps = false;

    protected $primaryKey = 'id';
}