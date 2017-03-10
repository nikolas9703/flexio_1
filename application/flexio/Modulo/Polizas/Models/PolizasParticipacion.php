<?php
namespace Flexio\Modulo\Polizas\Models;

use Illuminate\Database\Eloquent\Model as Model;

class PolizasParticipacion extends Model {

    protected $table = 'pol_poliza_participacion';
    protected $fillable = ['id_poliza', 'agente', 'agente_id','porcentaje_participacion'];
    protected $guarded = false;


}