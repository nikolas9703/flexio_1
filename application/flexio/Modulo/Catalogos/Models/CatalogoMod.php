<?php

namespace Flexio\Modulo\Catalogos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class CatalogoMod extends Model {

    protected $table = 'mod_catalogos';
    public $timestamps = false;
    protected $fillable = ['identificador', 'valor', 'etiqueta', 'orden', 'activo'];
    protected $guarded = ['id_cat'];
    protected $casts = [
        'activo' => 'boolean',
    ];

}
