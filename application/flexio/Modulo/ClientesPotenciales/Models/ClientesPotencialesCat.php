<?php
namespace Flexio\Modulo\ClientesPotenciales\Models;

use \Illuminate\Database\Eloquent\Model as Model;

class ClientesPotencialesCat extends Model
{
    protected $table        = 'cp_clientes_potenciales_cat';
    protected $fillable     = ['id_cat','id_campo','valor','etiqueta'];
    protected $guarded      = ['id_cat'];
    protected $primaryKey   = "id_cat";
    public $timestamps      = false;
}
