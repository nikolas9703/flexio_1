<?php
namespace Flexio\Modulo\ClientesPotenciales\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Correos extends Model
{

    protected $table = 'cp_clientes_potenciales_correos';
    protected $fillable = ['correo','tipo'];
    protected $guarded = ['id','id_cliente_potencial'];
  //  public $timestamps      = true;



}
