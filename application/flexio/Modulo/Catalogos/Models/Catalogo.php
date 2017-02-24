<?php
namespace Flexio\Modulo\Catalogos\Models;

use Illuminate\Database\Eloquent\Model as Model;

class Catalogo extends Model
{
    protected $table = 'flexio_catalogos';
    public $timestamps = false;
    protected $fillable = ['key','valor','etiqueta', 'tipo', 'orden', 'modulo','activo','con_acceso'];
    protected $guarded = ['id'];
    protected $casts = [
       'activo' => 'boolean',
   ];
}
