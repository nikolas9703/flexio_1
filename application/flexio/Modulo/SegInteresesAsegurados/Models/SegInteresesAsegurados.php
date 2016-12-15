<?php
namespace Flexio\Modulo\SegInteresesAsegurados\Models;

use Illuminate\Database\Eloquent\Model as Model;

class SegInteresesAsegurados extends Model
{
    protected $table        = 'mod_catalogos';
    protected $fillable     = ['identificador', 'valor','etiqueta','orden','activo'];
    protected $guarded      = ['id_cat'];
    protected $primaryKey   = "id_cat";
    public $timestamps      = false;
    
}