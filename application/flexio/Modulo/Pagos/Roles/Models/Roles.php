<?php

namespace Flexio\Modulo\Roles\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class Roles extends Model {

    protected $table = 'roles';
    protected $fillable = ['empresa_id', 'nombre', 'descripcion', 'superuser', 'default'];
    protected $guarded = ['id'];

}
