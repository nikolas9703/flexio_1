<?php

namespace Flexio\Modulo\Planes\Models;

use Illuminate\Database\Eloquent\Model as Model;
class PlanesComisiones extends Model

{

	protected $table        = 'seg_planes_comisiones';    
    protected $fillable     = ['uuid_comisiones', 'inicio', 'fin', 'comision','sobre_comision', 'update_at', 'created_at','id_planes'];
    protected $guarded      = ['id'];

    
}