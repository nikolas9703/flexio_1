<?php

namespace Flexio\Modulo\CatalogoTPoliza\Models;

use Illuminate\Database\Eloquent\Model as Model;

class CatalogoTPoliza extends Model {

    protected $table = 'seg_ramos_tipo_poliza';
    protected $fillable = ['nombre', 'created_by', 'update_at', 'created_at'];
    protected $guarded = ['id'];

    //scopes
}
